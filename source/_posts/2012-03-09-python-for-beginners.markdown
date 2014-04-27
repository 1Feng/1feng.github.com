---
layout: post
title: "Python for Beginners"
subtitle: How I wish we could onboard Python programmers
date: 2012-03-09 08:33
comments: true
categories:
---
Yesterday I attended the [Pycon Web Summit] and there was a lot of talk about getting new programmers started in Python.  I've been thinking about this a lot the last year since helping found the [Austin Web Python User's Group][AWPUG] and I think I have a solution.

### Success early, success often
One of the key things we need to be able to do is get developers on **every** platform up and running quickly.  An [iPython] shell is a wonderful place for a newbie.  Do you remember the first time you typed code into a REPL and it did what you told it to.  `2 + 2` returned `4`, then I'll bet you tried `2 + 3` just to see that it wasn't some trick.  That sense of wonder, excitement, and, most importantly, accomplishment needs to be priority number one as we move forward whether we're starting someone on raw Python for data manipulation or Django for full web application development.

The number of steps between "I want to learn to do X" and actually making Python do something for you needs to be minimal.  That means the first words can't be "pip install django" to get Django installed.  We need to teach newbies about pip and virtualenv and how to install Python and all of the steps that go in betwee, but not yet.

### The tool
I think an environment built on top of [Vagrant] is the right solution.  We can bootstrap a virtual machine that's ready to start accomplishing things the second it's launched.  We *can't* start teaching Python by telling people they have to go find, download, and install Ruby, Rubygems, VirtualBox, and Vagrant.

The solution to this problem is a one-click installer that gets Vagrant and all of its dependencies installed and presents you with a GUI to select the type of environment you want to create.  Need Django, Pyramid, NumPy, SciPy, or hell, even a setup with [csvkit]?  Select that and a few minutes later (assuming a broadband connection), you're up and running with a prompt that lets you start working.

This is doable.  I haven't done it.  I'm not sure that I could (I haven't programmed for Windows in well over a decade).  I want this out there though.  I want more people thinking about it and hopefully someone can kick the process off.  I'll help in any way I can and I'll definitely use it if someone starts the project.

Got a better idea?  Let's hear it.

[Pycon Web Summit]: https://us.pycon.org/2012/community/WebDevSummit/
[AWPUG]: http://www.meetup.com/austinwebpythonusergroup/
[iPython]: http://ipython.org/
[Vagrant]: http://vagrantup.com
