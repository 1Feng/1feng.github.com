---
layout: post
title: "The Case for Django"
date: 2014-03-03 13:42
comments: true
categories: 
  - opensource
  - django
  - python
  - flask
  - running
---
I get asked a lot where to start if you're looking to python for web backed work. A lot of people look at [Django][] and [Flask][] and feel that Flask is where they should start. It's nice and small, very simple, and after all they're not doing anything big and complicated, so why start with a big, complicated framework?

This reminds me if something that happens in the running world. People get started running then either a) read [Born to Run][1], or b) hear someone talking about the benefits of so-called barefoot running. (For the record, I've only seen a few people actually run barefoot. Most run with minimalist shoes like Vibram FiveFingers&trade;.)

There are many benefits to running with minimal shoes. Proponents point to studies that show lower injury rates amongst bare footers. They talk about our natural instinct to run and how the modern shoe with all of its support and cushioning is actually doing more harm than good.

The next part of their pitch is ignored by many of the so-called Born-to-Runners: it takes a lot of practice to be able to get to the point where you can run 10k, much less an ultra-marathon with minimal shoes. You practically have to start over and slowly build. There is a huge payoff, but it takes time. Otherwise, you're more likely to injure yourself.

I'm speaking from experience. I didn't read Born to Run, but I know the claims. When I started running a few years ago, I switched on and off from a minimal pair of running shoes and a pair of FiveFingers&trade;. I figured since I was just starting out I wouldn't have any bad habits to break.

There was one snag in my plan: I wasn't ready for them. I hadn't built up the running specific muscles. My form wasn't there yet. I quickly started having plantar fasciitis issues. They weren't debilitating, but enough to make me take a week off to rest and work on stretching. It flared right back up as soon as I started running again. I had a half marathon a few months out so something had to give. A trip to the running store and about $100 later I had a pair of running shoes that felt like pillows on my feet and a week later the pain was completely gone.

The same thing applies to web frameworks. It might seem like a good idea to stick with frameworks that can be coded in one file, or ones that don't do everything. Those frameworks are built on top of a lot of hard won lessons.

When you're starting out, you don't know what a properly factored web application looks like (yet). You don't know where to draw the line between your model and controller layers (yet). You don't really know the trade-offs involved in going with a relational database and a NoSQL database. And that's ok. Micro frameworks assume you do, though. They give you a lot (or a little, depending on how you look at it) of rope and it's really easy to end up with your app looking an awful lot like a noose.

So skip the minimalist when starting out, whether that's shoes or web frameworks. Build on the experience of others, then start stripping away those layers once you've got a solid base.

[Django]: https://www.djangoproject.com/
[Flask]: http://flask.pocoo.org/
[1]: http://en.wikipedia.org/wiki/Born_to_Run_(book)#Born_to_Run
