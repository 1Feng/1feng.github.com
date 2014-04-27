---
layout: post
title: Real-life global hell
subtitle: Or, why global state is bad by example
categories: 
  - python
  - globals
  - debugging
  - best practices
---

Lately I've been playing with testing frameworks all over the spectrum of
languages.  I've come to really enjoy using [Cucumber][] for testing web APIs.
Since most of my coding lately has been in JavaScript or Python, using Ruby
with Cucumber allows me to completely segregate my tests from the system under
test (SUT).  This separation has worked great until recently when I needed to
have the test system running in Python.

I started looking at the Python bridge in Cucumber and that's when I came
across [Lettuce][].  It's a Cucumber-inspired, Python <abbr title="behavior
driven development">BDD</abbr> library.  I like the syntax, it had built in
Django support, tons of tests (including functional and integration), so I
was ready to go.

Then I ran the test suite.  And it failed.

A failing test suite is a massive red-flag for me with any project.  In a test
suite, it's a nuclear launch siren.  I poked around a bit and figured out what
was triggering the test, but not why, [opened a bug report][issue 52] and found
out that the tests were never meant to be run together.

I let it go, but last weekend decided I was going to dig into the framework,
figure out what was causing the tests to fail if the functional tests were run
before the unit tests, and submit a patch.

I spent the better part of Sunday afternoon cursing at code, trying various
paths of exploration, trying to grok the entire framework's codebase to
understand what was happening.  I ended up going so far as doing the equivalent
of `var_dump()` debugging (pdb didn't prove very helpful because of the intense
setup required before the tests started running)

Finding the code that was causing the problem was easy - modifications to the
`lettuce.registry.STEP_REGISTRY` were causing the failure.  Figuring out how to
fix that proved more difficult.

The issue, it turned out, was global state.  The unit tests assumed that once
they setup, the state wouldn't change.  The functional tests didn't much care
for that and stomped all over the registry of steps.  By the time the unit
tests rolled around, the steps that were so carefully defined inside the unit
test modules were no gone and the test suite was throwing failures.

I finally [landed on the solution][7f6e852] by redefining all of the steps
inside a `@with_setup()` for the tests that need them in the unit tests.  It
brings up a couple of interesting learning moments.

First, this shows the need to make no assumptions when writing tests.  Need a
database connection?  Make sure its initialized and ready for each test that
uses it.  Want to make sure a step is defined for the test your checking in a
BDD framework, define it immediately before running the test.  It's a good
example of defensive coding.

Second, it shows the mess that global state can create.  Each of the test
modules was being loaded by [nose][], then the tests were being executed after
a global state (the `STEP_REGISTRY` object) was defined.  When other tests
changed that state, things started falsely failing.

The "fix" currently is to reset that state to what you expect every time, but
this causes issues with tests running in parallel.  What happens when two of
the same tests both try to reset the state at the same time?  Don't know, I
haven't tried it yet, but I imagine it's gonna cause some more failures.

My fix now is short-term (and [was included][0bf1f87]) and it gets the job
done.  Hopefully, this shows you a bit about what we mean when developers say
that global state is a bad thing that leads to tricky bugs that are hard to
comprehend.  In this case, I literally had to understand the entire step
definition system in order to comprehend what was happening here

The test cases at least gave me some guard rails to help guide me toward the
solution, but had there not be global state in the first place, these tests
would have worked across the board with no problems.


[Cucumber]: http://cukes.info
[Lettuce]: http://lettuce.it/
[issue 52]: http://github.com/gabrielfalcao/lettuce/issues/52
[7f6e852]: http://github.com/tswicegood/lettuce/commit/7f6e852febed135fd5d15081facdfa05c8010823
[0bf1f87]: http://github.com/gabrielfalcao/lettuce/commit/0bf1f87115567648eecbe601c66217e39d6a9178
[nose]: http://somethingaboutorange.com/mrl/projects/nose/
