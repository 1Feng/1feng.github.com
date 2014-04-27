---
layout: post
title: Enough Complaining
subtitle: It's all bad code.  Can we move on?
categories:
  - code
  - quality
  - philosophy
---


I've spent a lot of time working on a lot of different code.  I'm fortunate in
that I've been exposed to all three of the modern scripting languages and their
communities.  All of them, to varying degrees, bemoan their code.  All of them,
way too much of, bemoan the code of everyone else.

> Ruby is too clever, I can never figure out what's going on or what to expect.

> Python developer's can't write a test to save their lives.

> PHP developers, well, do I really need to say more?

Everyone's moaning, from the top to the bottom about something with software.
To listen to them, you'd think we had no tools to do anything and that all
software sucked.

I've got news for you.  It all does, so can we please move on now?

This isn't a new concept.  Actually, I'm stealing it mostly from [Chris][] and
his keynote as [Ruby Midwest][], but I'm coming at it from a slightly different
point of view.

I had the unfortunate task of talking with a client today about the state of
their code and recommending that they put a few small tweaks on hold until a
larger revisiting could happen.  Ultimately, it's their decision and I'll do
what they want, but it's going to cost them more for what can only be a
temporary solution now, versus revisiting it in a few months as part of a
larger redesign.

Why?  Because their current code <em>sucks</em>.  Everything is hard
coded---nary a loop in sight.  There's HTML mixed up in their business logic
and business logic in their HTML.  They have a haphazard layout to their
project.  Form validation?  They got it.  To the tune of `if` statements in
functions that are hundreds of lines line.  Touching one part invariably breaks
two other parts because they depended on that particular HTML tag to exist in
that particular place, with those types of siblings, etc.  etc., etc.

This code is the definition of spaghetti code.  Adding features that are less
than 30 minutes on any normal project are taking upwards of 5 to 6 hours, most
of it in testing and fixes for other things you break along the way.

I'm relatively new to the project and wasn't brought on in a code review
capacity.  I was explaining my reasoning and a knowing grin came across their
face.  "Well, I've been told that most developers hate projects that they come
into when they're new to it because they didn't code it.  It's not how they
would have done it."

Gee, thanks.  Every single developer out there who's complained because someone
used a different bracing system or they used a different naming convention than
they would have or any one of the hundreds (if not thousands) of other asinine
things coders like to complain about make it impossible for me to convey to a
new client that his code truly is in need of some serious help because
"developers hate projects."


### Can we all please shut up?

My point is this.  Code sucks.  It's all a compromise to take an idea that we
(or our clients) have formed and turn it into something machines can
understand.  It's all trade offs.  There's always another way to approach
something, a different language it could be written in, a more scalable data
back end that could have been used.  <em>Always</em>.

So why spend time talking about it?  All we're doing is making it easier for
the people who need to be listening to us to tune us out.  They don't know the
difference someone like me complaining about the internal structure of
[Wordpress][] (which I think is an abomination) and my explaining that it is
the wrong choice for their project (which I'd argue is the case for at least
half of business deployments of WP).  That's not their job to known and
understand the difference, but it is ours.  It's also our job to be able to
convey that information to them.

This reminds me of a few tweets from [Ivo][] (if I recall correctly) during
[Confoo][].  One of the speakers had tweeted about the wifi and issues they
were having with it.  Then a bunch of people started "me too" tweeting about
how bad it was and Ivo called the original person out on it.  What good did it
do for a speaker to complain publicly about a situation that was already known
(and being worked on, I might add) other than to encourage everyone else to
follow suit?

We're doing the same thing every time we bitch and moan about relatively
insignificant things in code.  We're doing everyone a disservice by increasing
the noise-to-signal ratio when we complain for no good reason.  So please,
let's all put a sock in it.

<fieldset><legend>Author's note</legend>
<p>
This is addressed just as much to me as it is to the next guy.  I'm just as guilty.
That's why I say "we" throughout.
</p>
</fieldset>

[Chris]: http://chriswanstrath.com/
[Ruby Midwest]: http://rubymidwest.com/
[Wordpress]: http://wordpress.org/
[Ivo]: http://www.jansch.nl/
[Confoo]: http://confoo.ca/en

