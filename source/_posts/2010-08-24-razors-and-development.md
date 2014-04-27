---
layout: post
title: Razors and Development
subtitle: Story of my razors
categories:
  - development
  - methodology
  - best practices
---
A few years ago I switched to an old fashion safety razor and haven't looked
back.  The latest entry into the razor market has reaffirmed my decision as the
right one.

The new [Pro Glide][] from Gillette only costs less than $10 to purchase.  Good
deal, right?  Nope.  The replacement blades cost $3-$4 each!  Assuming you get
a few weeks out of each blade, you're looking at paying between $6 and $10
every month to use this razor blade.

It's a great deal---for Gillette.

I use [Merkur razor][].  I paid a lot, comparatively speaking, up front but I
can buy better quality razors for less than $0.75 each.  They last a lot longer
and I end up with a much better shave.

I view the trade-off here as the same one you have to look at when deciding
what framework you choose to develop your code in.  There are a lot of
frameworks that provide a lot of help getting off of the ground.  It almost
seems too easy.

Write your on custom blog in 5 minutes?  Sure!  Want to have a RESTful API?
Add a couple of classes, some new routes, mark as complete.

Look at the framework and read some of the comments from its detractors.  Those
complaining generally have one of two problems:

1. They're going to complain about anything, they're just ranting.  Ignore
   these people.
2. They've hit a legitimate pain point in the framework where they deviated too
   far from the intended use.  Pay attention to what these people are talking
   about.

If you're application is significantly complex, no off-the-shelf framework is
going to do everything you need it to.  Some frameworks may even get in the
way.  Make sure you realize the trade-offs before you commit.


### What makes a good framework?

The best ones serve as [scaffolding][]---in the original meaning.

> ... a temporary structure used to support people and material in the
> construction or repair of buildings and other large structures.

Put another way for software development:

> ... helps you ramp up quickly, then gets out of the way.

Historically, frameworks manage the first part of this well.  That's where
they shine.  It's the last part that they've had a problem with.

[Django][] manages both of these well.  My one complaint with it is that it
manages the latter part better than the first.  There's a lot of boilerplate
needed to get started, but I can live with that.  When my applications outgrow
Django, removing Django from the equation is easy with one exception.

Models.

Models are like your razor's blades.  Without blades, your razor doesn't shave;
without models your application doesn't have any data to work with.  The fix
I've found works best for me is to keep my models then and put all of my logic
for operating on them in other areas of the code base.

This separation helps me keep my business logic portable.  I might be using the
cheap route to get started, but the heavy lifting goes with me if I decide
something else is a better fit.


[Pro Glide]: http://www.amazon.com/dp/B003983HRI/
[Merkur razor]: http://www.amazon.com/dp/B0028FNNI6/
[scaffolding]: http://en.wikipedia.org/wiki/Scaffolding
[Django]: http://www.djangoproject.com/
