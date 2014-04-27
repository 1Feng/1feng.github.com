---
layout: post
title: "Generic Dangers"
subtitle: "Beware of using too generic of a solution"
date: 2012-09-30 11:08
comments: true
categories: 
  - opensource
  - django
---
Here at the Texas Tribune, we started using a project called [django-chunks][]
some time last year.  Consider this post a cautionary tale and think long and
hard before you start using.  We didn't.  We're paying the price.

## The Promise
django-chunks gives you the ability to inject arbitrary chunks of HTML into any
template inside Django.  You load up a template tag library, call a
templatetag, and you're off to the races.  No more waiting on clients (or other
departments) to get you copy.  "Here's the chunk, go to town," you tell them.

That seems pretty good, right?  We're all lazy, that's why we program.  The
idea of making a computer do something for us tends to be at the core of why
most programmers got into programming.  Second only to making computers do the
work is making other people do the work, so django-chunks let's us off load
that work to someone else.

## The Problem
Once you've started making things chunks, everything becomes a chunk.  It's a
golden hammer of sorts.

> This field needs to be copy edited once in its entire existence?  Bring in
> the chunks!

This type of thinking is shortsighted at best, and harmful at worst.  This
morning I took a look at our [join][] page only to discover that we were giving
users a security error due to a mishandled chunk.  An image URL had been
entered as `http` instead of `https`, mixing insecure content in to a secure
page.  Yes, there was a chunk created who's sole purpose in life was to be
edited to change out an `<img>` tag!

## The Solution
Don't use chunks.  The case above is where a simple model should have been used
if we really need to let non-technical people change out the image.  We have a
hard requirement on `https` on that page, and as programmers we can enforce
that.

Django is meant for building custom stuff.  Go do that.  Use things that help
you build that custom stuff faster, but don't go looking for turn-key.  Someone
else's answer to the problem isn't going to be as good as what you could have
built.


[django-chunks]: https://github.com/clintecker/django-chunks
[join]: https://www.texastribune.org/join/
