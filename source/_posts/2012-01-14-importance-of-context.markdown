---
layout: post
title: "Importance of Context"
subtitle: "Why Django is better than Rails, or Rails > Django, or Django > PHP, etc, etc, etc, ad infinitum, is is pointless."
date: 2012-01-14 14:20
comments: true
categories: 
  - opensource
  - programming
  - frameworks
---
Today I discovered the [99% Invisible][] podcast on architecture and design.
Their latest podcast, [Pruitt–Igoe Myth][], tackles the problems associated
with the Pruitt–Igoe housing project which was built in the 1950s in St. Louis
to provide affordable housing in the St. Louis urban core.  Due to a variety of
reasons, which the podcast explores, it was torn down in the 1970s.  From
Wikipedia:

> [Pruitt-Igoe's] 33 buildings were torn down in the mid-1970s, and the project
> has become an icon of urban renewal and public-policy planning failure.

After listening to the podcast, you come away with the impression that this
isn't a fair assessment.  It was built at the beginning of the [White Flight][],
in a part of the city that saw a decrease in population, not the projected
100,000 every decade increase that was forecasted.  These and other issues
contributed to it turning into the very thing it was trying to prevent: a slum.

The building is considered the example of the failure of [Modernist architecture][]
as it applied public house, but if you view it in the context above you can see
that there are many external factors that contributed.  It's easy to pick one
particular piece of the puzzle and lay the blame on that for the failure.  It's
much harder to try and understand the complex relationship around what caused
the issue.


Applied to Programming
----------------------
This type of logical error is present in many (not all, but many) of the
conversations about what framework or language to use, what methodology should
be adopted, or even where to found your startup.  It's easy to point to one
success or failure and declare "X is why Z happened, so if I want to duplicate
Z, then I must/must not do X."  This type of [cargo-cult][] behavior is
dangerous and should be guarded against.

Yesterday I tweeted this:

> Whoa! JustinTV is moving from #rails to #django. I'm telling ya, Python & the
> web with a little Django mixed in is about to blow up.

It gives the impression of just that type of "Y leads to X" kind of thought
process that I'm against.  To clarify, I whole-hearted expected what [kvogt][]
wrote when explaining why they're moving to Django.  To paraphrase: "it just
makes sense right now to be on one platform."  Justin.tv isn't going to suddenly
take the world by storm after moving to Django any quicker than they would have
if they had moved their Python backend to Ruby.

That said, I stand behind the final point of that tweet.  There are tons of
shops using Python and Django that aren't vocal about their use.  Python is
powering business logic that runs on servers sending me music, tracking my
location, displaying my news, and a whole host of other things.  Python can do
everything low-level system tasks to scientific and financial analytical
calculations to high-level business logic for websites and everything in
between.

I can't help but thing there's going to be more Justin.tv-style announcements
this year: shops standardizing on one language and that one language being
Python.

[Pruitt–Igoe Myth]: http://99percentinvisible.org/post/15382659055/episode-44-the-pruitt-igoe-myth
[99% Invisible]: http://99percentinvisible.org/
[White Flight]: http://en.wikipedia.org/wiki/White_flight
[Modernist architecture]: http://en.wikipedia.org/wiki/Modernist_architecture
[programming]: http://www.travisswicegood.com/tags/programming/
[cargo-cult]: http://en.wikipedia.org/wiki/Cargo_cult
[kvogt]: http://news.ycombinator.com/item?id=3461476

