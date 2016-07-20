---
layout: post
title: "signal with mutex locked or not(译)"
date: 2016-07-20 16:48
comments: true
categories: 
---
## signal with mutex locked or not?


Introduction
####介绍

A perennial question arises when we use condition variables: should I signal/broadcast before or after unlocking the mutex?

当我们使用条件变量的时候，总有这样一个问题：到底该在解锁mutex之前进行sinal／broadcast，还是在之后？


When to signal?
什么时候signal？

``` c
pthread_mutex_lock(&mutex);
predicate=true;
pthread_cond_signal(&cv);    // OR: pthread_mutex_unlock(&mutex);
pthread_mutex_unlock(&mutex); //  : pthread_cond_signal(&cv);

```

The Authoritative Answer

Accordingly to SUS7:
[SUS7](http://pubs.opengroup.org/onlinepubs/9699919799/functions/pthread_cond_signal.html)的权威解答如下：
>>
The pthread_cond_broadcast() or pthread_cond_signal() functions may be called by a thread whether or not it currently owns the mutex that threads calling pthread_cond_wait() or pthread_cond_timedwait() have associated with the condition variable during their waits; however, if predictable scheduling behavior is required, then that mutex shall be locked by the thread calling pthread_cond_broadcast() or pthread_cond_signal().


译注：
>>
当其他线程利用pthread_cond_wait() 或pthread_cond_timedwait() 在关联的条件变量上等待时，当前线程既可以在持有（锁住）mutex时调用pthread_cond_broadcast()或 pthread_cond_signal() ，也可以在不持有mutex的情况下调用。然而，如果需要可预测的线程调度行为，则需要在mutex被锁住的情况下调用pthread_cond_broadcast() 或 pthread_cond_signal()



Alright, but what does it mean concretely?
上述具体时什么意思呢？

Signal with Mutex Locked

####锁住mutex时进行signal

On some platforms, the OS performs a context switch to the woken thread right after the signal/broadcast operation, to minimize latency. On a single processor system, this may lead to unnecessary context switches if we signal or broadcast while holding the mutex.

在某些平台上，OS就在 signal/boadcast 之后进行上下文切换（context switch）来唤醒等待线程以达到降低延迟的效果。在一个单处理的系统上，如果我们在持有mutex的情况signal/broadcast则会导致不必要的上下文切换（context switch）.

Fig 1- signal with mutex locked. We get 2 unnecessary context switch.
Fig 1- 锁住mutex时进行signal，我们造成了两次不必要的上下文切换（context switch）

Indeed, consider the scenario shown in figure 1. The thread T2 is blocked on the condition variable. T1 signals the condition while holding the associated mutex. A context switch to T2 occurs and T2 wakes up. But before returning from pthread_cond_wait, T2 needs to lock the mutex. However that mutex is already hold by T1. As a result T2 blocks (but this time contends for the mutex) and a context switch to T1 occurs. Then T1 unlocks the mutex, and T2 becomes finally runnable. The situation appears to be even worse, if we broadcast the condition variable to several threads.

事实上，考虑figure 1中的场景。线程T2阻塞在条件变量上。当 T1 在持有关联的mutex的情况下 signal 条件变量时，上下文切换至T2导致T2被唤醒。但是在pthread_cond_wait返回之前，T2需要锁住条件变量关联的mutex。然而条件变量关联的mutex此时仍被T1持有（锁住）。结果导致T2被阻塞（？？mutex竞争）并且上下文切换至T1。之后T1解锁条件变量关联的mutex，同时T2最终变为runalbe状态。当我们对多个线程进行条件变量broadcast（多播）时，情况会变的更加糟糕。

Some Pthreads implementations address this deficiency using an optimization called wait morphing[1]. This optimization moves directly the threads from the condition variable queue to the mutex queue without context switch, when the mutex is locked. For instance, NPTL implements a similar technique to optimize the broadcast operation[2].
有些Pthreds 使用名为 **wait morphing** `[1]`的优化实现方式来应对这个缺陷。这种优化可以在持有锁的情况下避免上下文切换(context swith)直接将线程从条件变量队列转移至mutex队列。例如 NPTL 使用了**类似的技术**`[2]`来优化broadcat.

When our Pthreads implementation does not have wait morphing, we may want to unlock first and signal/broadcast after. Indeed, the unlock operation doesn’t cause a context switch to T2, since that later thread is blocked on the condition variable. When T2 wakes up, it finds the mutex unlocked and can grab it.

当我们的实现没有使用 wait morphing时，我们可能需要先解锁然后在进行 signal/broadcast. 事实上，解锁操作不会导致上下文切换至T2，因为T2阻塞在了条件变量上。当signal/broadcast之后，T2被唤醒后会发现mutex已经被解锁，便可以持有mutex。

Signal with Mutex Unlocked
####解锁mutex后signal

Is there any disadvantages doing so? First we notice a difference of semantic. If we signal or broadcast first, we are sure to wake-up a thread blocked on the condition variable (assuming that there is such a thread). However, if we unlock first, we may instead wake up a thread blocked on the mutex.
这样（译者注：解锁mutex后signal）存在缺点么？首先我们关注一个不同的情形。如果我们先signal或者broadcast，我们可以确保唤醒一个阻塞在条件变量上的线程（假设存在这样一个线程）。然而，如果我们先解锁，我们可能会唤醒一个阻塞在mutex上的线程。


When can such situation occur? A thread may be blocked on the mutex, because:
什么时候会出现这种情形呢？一个thread可能阻塞在mutex上，因为：

- it was about to check the predicate, and eventually going to wait on the condition variable.
- it was about to change the predicate, and eventually inform the thread waiting on the condition variable

- 线程即将检查谓词（译者注：条件变量的等待条件），并且最终会等待在条件变量上
- 线程即将修改谓词，并且最终会通知等待在条件变量上的线程

注：http://fileadmin.cs.lth.se/cs/education/edan25/f05.pdf

In the first case, we may get an intercepted wake-up situation. Indeed, consider again the settings shown in figure 1, but with an third thread T3 blocked on the mutex. If T1 unlocks the mutex, a context switch to T3 may occur. Now T3 may see the predicate true, processes it accordingly, and eventually reset the predicate before T1 signals or broadcasts the condition variable. When T2 wakes up, the condition that triggered this wake-up may no longer hold. In a correctly designed application, this is not a problem since T2 has to account for spurious wake-up anyway. The program below shows that intercepted situation can occur.

在第一种情况下，我们可能获取一个被拦截的唤醒。事实上，再次考虑figure 1中的情形，但是存在第三个线程T3，T3阻塞在mutex上(`译注：即将检查谓词`)。当T1解锁mutex，上下文切换至T3。现在T3发现谓词为真，因此执行相应处理，并且最终会在T1 signal/broadcast 条件变量之前重置谓词。当T2被唤醒，出发唤醒的条件已经不存在。在一个正确的程序设计中，这不是一个问题，因为T2总是会应对假唤醒（译者注：即使被唤醒也会再次检查谓词）。下面的程序演示了被拦截的唤醒情形。

Download cv_01.c

In the second case, we eventually delay the wake-up of the thread T2. Indeed, T3 may notice that T1 changed the predicate already, and decide to not signal or broadcast again. As long as T1 isn’t scheduled and gets the chance to signal or broadcast, the thread T2 remains blocked.
在第二个例子里，我们延迟了T2的唤醒。实时上，T3可以发现T1已经修改了谓词，并且决定不再signal/broadcast。只要T1不被调度并且有机会signal/broadcast，那么T2会仍旧会阻塞（`没搞懂这句话`）。

Real-time Considerations
####实时性考虑

In real-time programming, the priority of a thread usually reflects the criticality of meeting the associated deadline. Roughly speaking, the more critical the deadline, the higher the priority. Not meeting a deadline may cause the system to fail, resulting possible damages or harms to the environment as discussed in my article Real World Systems.
在实时程序设计中，线程的优先级通常会被截止时间的临界所影响。 概括的说，越临近结束时间，优先级越高。不遵守时限可能会造成系统失败，结果可能会损坏我在之前[《Real World Sytems》](http://www.domaigne.com/blog/computing/real-world-systems/)中讨论的环境。

Given this, you definitively want that the highest priority thread gets the CPU as soon as it becomes runnable. There are however situations where a low priority thread may prevent a high priority thread to run. Such situations are called priority inversion. This occurs for instance when the high priority thread wants to lock a mutex already hold by a low priority thread. This is in practice not a problem, as long as the amount of time where the priority inversion occurs remains bounded and small. A more severe case is when the priority inversion is (potentially) unbounded. This can cause the high priority thread to miss its deadline, Protocols, like Priority ceiling or priority inheritance[3] have been devised to circumvent such a problem.
考虑这种情形，你明确的想要最高优先级的线程在变为runnable状态时可以尽快获取CPU时间片，但是低优先级的线程却可能阻止高优先级的线程运行，这种情形被称为优先级倒置。这种情况发生的一个例子就高优先级的线程想要锁住一个已经被低优先级线程持有的mutex。只要优先级倒置的持续时间总是很少或被限制的，这事实就不是一个问题。一个更严重的情况是优先级倒置（潜在的）不被限制, 这可能倒置高优先级的线程错过截止时间，像**优先级顶置**或**优先级继承** `[3]`这种协议就是被设计来规避这种问题的。

When using real-time scheduling policy, the signal/broadcast operation wakes-up the thread with the highest priority. If there are two or more threads with the same priority, the thread that blocked first on the condition variable is chosen. This is what we expect.
当使用实时调度策略时，signal/broadcast 操作唤醒最高优先级的线程。如果存在两个或更多线程拥有同样的优先级，会优先选择第一个阻塞在条件变量上的线程。这也是我们所期待的。

However, condition variables may be subject to unbounded priority inversion in three ways. The first obvious inversion is the mutex associated to the condition variable, since mutexes themselves are subject to unbounded priority inversion.
然而，条件变量可能被不做限制的优先级倒置以三种方式影响。第一种明显的倒置是条件变量关联的mutex，因为mutex自身就会被不做限制的优先级倒置所影响。

Another priority inversion can occur before the thread signal or broadcast the condition variable. Consider again the settings shown in figure 1, where T1 is a low priority P1 thread, and T2 a high priority P2 thread ( P1 < P2 ). As long as T1 doesn’t signal or broadcast, it can be preempted by a mid priority thread T3 with priority P3 where P1 < P3 < P2. In particular, T1 could be preempted between the unlock and the signal/broadcast operation, if it choses to unlock first. Eventually T1 cannot wake-up T2, and so the mid priority thread T3 prevents the high priority thread T2 to run. If we signal or broadcast first, we’re guaranteed that the thread T2 shall be scheduled as soon as T1 unlocked the mutex, assuming that or priority ceiling or inheritance protocol is used for the mutex. So it is slightly better to signal or broadcast while holding the mutex. Note however that in both cases, a priority inversion could occur while T1 is changing the predicate.
另外一种优先级倒置可以发生在线程signal/broadcast条件变量之前，再考虑figure 1中的情节，假设T1是一个低优先级（P1）的线程，T2是一个高优先级（P2）的线程（P1 < P2）。只要T1不signal/broadcast，他就就可以被中间优先级（P3）的线程T3抢占（P1 < P3 < P2）（`译者注：注意和前一种做区分，这个抢占是cpu正常调度引起的，不是因为解锁这个system call引起的，毕竟P3 > P1`）。通常情况，如果选择先解锁再signal/broadcast, T1可以在解锁之后和signal/broadcast之前被抢占。最终T1不会唤醒T2，因此T3阻止了比它优先级更高的T2运行。如果先signal/broadcast，再解锁，我们可以保证T1解锁之后T2可以尽快被调度到，前提是在mutex上使用优先级顶置或优先级继承协议（`译注：否则在解锁的时候就有可能发生拦截了`）。所以持有mutex时进行signal/broadcast稍微优于先unlock再singnal/broadcast(`其实我们想要的结果是不管是否先解锁，只要保证在T1进行signal/boadcast之后，T3可以尽快得到cpu时间片`)。值得注意的是不管怎样在这两种情形(`译注：即是否先解锁再signal/boadcast这两`)中，当T1正在修改谓词的时候优先级倒置都会发生（`译注：这种调度是正常的，毕竟P3>P1`）.

A related situation is when the thread T3 is blocked on the mutex. We discussed already this situation for time sharing scheduling in the previous section. If T1 unlocks before the waking up T2, T3 will acquire the CPU. In the intercepted wake-up case, the change of condition is processed by T3 at a lower priority than it would have been by T2. In the other case, we get again a potential unbounded priority inversion. 

一个相关的情形是，当T3阻塞在mutex上。我么已经在之前分时调度的章节（`解锁mutex后signal小节`）讨论过了。如果T1在唤醒T2之前解锁，T3将会获得CPU时间片。在拦截唤醒的例子中，条件被修改后本应该T2处理的却被优先级更低的T3给处理了。另一个例子中(`哪个啊？？`)，我们再次被潜在的不受限制的优先级倒置影响。

In the light of the previous explanations, we may believe that unlocking the mutex after the signal or broadcast operation is mandatory for real-time predictability. This judgment needs to be moderated. I never experienced myself situations where this scheme is obligatory to enforce predictability. It was always possible to change the design, so that unlocking first wasn’t a problem. In fact, David Butenhof wrote in a recent post that the real-time predictability statement in the standard is essentially political rather than technical[4]. It has been raised by members of the real-time working group, and was settle down in the standard to avoid potential objections during the balloting process.
根据之前的解释，我们相信在要想实时调度的结果可预测，signal/broadcast之后解锁mutex是必须的。这种判断应该有所节制，我从来没有经历过必须强制执行可预测性的情形。总是有可能去修改设计，所以先解锁未尝不可。事实上，David Butenhof 在最近的文章中写道，在SUS标准中的实时可预测陈述本质上是政治而不是技术。这已经被实时工作组的成员追捧起来，并且为了避免投票过程中潜在的反对也被SUS标准采纳。

A Trap
####一个陷阱

There is however one case where you can get into troubles when you unlock first. You must make sure that the condition variable you signal or broadcast still refers to a valid condition variable object after you unlocked the mutex. This sounds pretty obvious, but recognizing this fact in practice isn’t always easy[5]. In particular, the alarm bells should be set off if you are destroying the condition variable (or the memory holding the condition variable) in the woken thread(s).
存在一个案例，如果你先unlock会令你陷入问题中。你必须确保当你解锁mutex之后你所signal/broadcast的条件变量依旧引用的是一个有效的条件变量。这听起来似乎是显而易见的，但是在实践中一直确保这一点并不轻松`[5]`. 特别的，如果正在被唤醒的线程中销毁条件变量（亦或者条件变量所在的内存）则需要拉响警报了。


Download cv_02.c

On my box, the above program terminates with a SIGSEGV after a few thousands of iterations when running at least on two CPUs. The problem lies at lines 56-59. It can happen that the shutdown thread sees that nthreads==0, and thus destroys the condition variable, But shortly after a thread in the pool tries to signal the condition variable that doesn’t exist anymore. If we signal first and then unlock, the program runs fine.
上述程序运行在至少双核的机器上，在反复几千次后被SIGSEGV信号所终止。问题在56-59行。问题发生在在负责停止的线程发现nthread为0时会销毁条件变量，但是稍后在线程池中的线程会尝试signal已经不存在的条件变量。如果我们先signal然后再解锁，程序运行就不会有问题。


Conclusion
####结论

I personally prefer to signal or broadcast while holding the mutex. First, I may avoid some obscure bugs. Second, doing so has almost no performance impact on Pthreads implementation that use wait morphing optimization. Third, and more importantly, I consider moving the unlock before the signal/broadcast if and only if profiling exhibits substantial performance boost. There is no point in optimizing something that doesn’t contribute to my bottlenecks.

我个人选择在持有mutex的情况下进行signal/boadcast.首先，我可以避免一些晦涩的bug。其次，这样做在使用wait morphing优化实现的pthread中几乎没有性能影响。再者，更重要的一点，我认为当且仅当经过分析有明显的性能提升时才会将解锁放在signal/boadcast之前。对瓶颈没有贡献的优化是没有意义的。

Notes and further Readings

- `[1]` David R. Butenhof: Programming with POSIX Threads, section 3.3.3, pp 82-83, Addison-Wesley, ISBN-13 978-0-201-63392-4.]
- `[2]` [Ulrich Drepper. Futexes Are Tricky](https://www.akkadia.org/drepper/futex.pdf). A paper about futex, the Linux kernel object behind condition variable. See in particular the Chapter 8 “Optimizing Wakeup” on page 9-10.
- `[3]` [Kyle & Bill Renwick. How to use priority inheritance](http://www.cs.rice.edu/~mgricken/teaching/402/09-spring/readings/PriorityInversion.html). An excellent article from embedded.com about priority inversion and possible cures.
- `[4]` [basic question about concurrency](https://groups.google.com/forum/#!topic/comp.programming.threads/wEUgPq541v8). A discussion thread on c.p.t, where David Butenhof explains what the “predictable scheduling” statement in the SUS standard means and where it comes from.
- `[5]` [A word of caution when juggling pthread_cond_signal/pthread_mutex_unlock](http://groups.google.de/group/comp.programming.threads/browse_thread/thread/23dd5883dc36d14a/7e5fcdf360543375) . A post from Bryan Ischo on c.p.t. that shows a subtle bug when unlocking before signaling.

Like

2 people like this. Sign Up to see what your friends like.

Vimium has been updated to 1.49.

×
