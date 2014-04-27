---
layout: post
title: "Open Source Licenses"
date: 2013-03-06 10:32
comments: true
categories: 
  - opensource
  - journalism
  - licenses
---
[IANAL][], but I like to pretend like I am on the Internets.  This past week at
[NICAR][], the discussion of open source licenses came up in one of the evening
tracks over a few bourbons, or it might have been wine by that point, but I
digress.  The general theme: *licenses are confusing.*

I know a little bit about them I'm hoping to shed some light on them for fellow
journalisty type developers who are thinking about releasing their code but
aren't sure which license they should use.

Caveats and such: I'm *seriously not a lawyer*, this isn't legal advise, and so
on, et cetera.  Please talk to one if you have serious legal questions.

## Range of Licenses
There are [69 official open source licenses][alpha-license-list] in use.  There
are many, many more that are snowflake licenses---licenses that have provisions
that are unique to them.  Many companies, including ones that I've worked for
in the past, have created custom licenses by modifying one of the main open
source licenses.  Many of these have been written by lawyers, but snowflake
licenses are an unknown quantity until they've been tried in court.

You should avoid snowflake licenses for your open source code.  Having a
license that is unique to your project increases the barrier to entry.  Each
developer has to read and understand the license and try to tease out any
differences you have with the more common licenses.

Instead of going the snowflake route, opt for one of the [popular open source
licenses][popular-license] that are commonly used.  Each of the licenses have
their place, but I'm going to touch on the three that are the most common and
one additional license that I think journalists should be familiar with.

## GPL: The Viral License
GPL, the Gnu Public License is possibly the most popular and familiar of the
open-source licenses.  It's the license that the [Linux Kernel][] and many of
the tools that ship with the Linux operating system are released under as well
as the wildly popular [WordPress][] blogging platform.  I can distribute GPL
software any way I want.  I can give it away, I can charge, I can do some
hybrid of those two.  One thing I can't do is limit what you do with it after
you acquire it.

The GPL is a copyleft license, sometimes referred to as a viral license.  It's
viral because it forces your hand when it comes to licensing derivative works.
Any derivative software must be distributed a compatible license like the GPL.
In other words, if I came up with a way to modify Linux and wanted to
distribute it, I would have to distribute it under the GPL license.  That
distribution could be paid, but anyone who pays for it could then redistribute
it at will.

[GPLv3][] has some interesting provisions to.  Namely, the Additional Terms.
These are optional things that the author can add.  For example, 7b requires
"preservation of… author attributions" in a project.  This is useful for
businesses who want to release their software, but want make sure that their
competitors can't do a find-and-replace for their competitor's name and
repackage the software as their own and have to fully credit them, including
displaying logos in the user-interface and such.

## New BSD and MIT: Do what you will
On the other end of the spectrum are the New BSD (more commonly referred to
simply as BSD) and the MIT licenses.  These two licenses are much more
permissive, allowing redistribution with only minor restrictions.

The MIT simply requires that the copyright notice be transmitted with "all
copies or substantial portions of the Software."  Essentially, you have to tell
the outside world that the software you're distributing contains the MIT
licensed software.    Both [Backbone.js][] and [Underscore.js][], two
JavaScript projects that originated in the [DocumentCloud][] project, are
licensed as MIT.

The New BSD license says the same thing, plus one other clause that says you
can't reuse the original package's name nor the names of any of the
contributors to "endorse or promote products derived from this software without
specific prior written permission."  FreeBSD and OpenBSD use the BSD license as
does [Django][].

## Licenses and Communities
My thoughts on licenses have evolved over the years.  [Jacob Kaplan-Moss][JKM]
introduced me to the idea of thinking of licenses as a community identifiers
(Side note: he was introduced to this thought process by [Van Lindberg][], the
current [PSF][] Chairman and author of the book [Intellectual Property and Open
Source][]).  All communities have certain things that they use to identify
those who they have a common interest with.  Rockabillies have fashion sense
and a music that's unique to them.  Gangs have the color of their clothes.
Developers have their languages and their licenses.

Each sub-community in the open-source community have their preferred license.
For example, jQuery is dual-licensed as GPL/MIT, so most developers releasing
software for jQuery use a similar license.  The JavaScript and Ruby community
tend to use the MIT license, as is evidenced by the amount of MIT code on
[npmjs.org][] and [Rails][].  The [Python][] community, and particularly the
[Django][] have a bias toward BSD.

Releasing software meant to be a part of those communities without following
the cultural norms within those communities is a sure way to stick out.  It's
like walking into a rockabilly bar dressed in a suit.  You should always have a
good reason for bucking the norm within a community that you want to be a part
of.  Trying to release GPL licensed code that builds on top of Django means
that you're not part of the community---you've set yourself up as an outsider.

Releasing your software with a more restrictive license than is common in a
community that you're trying to participate in also means you're placing
further restrictions on those in the community.  You can use their BSD or MIT
licensed code, but they can't use your GPL code in their projects.  That's
essentially telling the other developers that you love their contribution, but
not enough to let them use what you've built under an equally permission
license.

## So what to use?
This is where I should mention discussions of being in Rome and so on, however,
I think you should use another license: [Apache License 2.0][].  Apache is
essentially a BSD license with two very distinct modifications.

1. Any contribution to the project is considered to be made under the terms of
the Apache License.  [Contributor License Agreements][] (CLAs) can be used to
enforce something similar with BSD or MIT licenses, but they aren't guaranteed.
The Apache License bakes the terms of the contribution in by default.  1.
Apache grants a full rights to any current or future patents that might be
derived from the contribution.

This last part is *the* reason to use Apache.  When we started the [Armstrong
Project][], I called up [Jacob Kaplan-Moss][JKM] to ask his opinions on
licenses.  He sold me on Apache with this line:

> If I had [the licensing of Django] to do over again, it would be Apache
today.

JKM's endorsement on the grounds of patent protection was the reason that I
advocated to use the Apache License on the [Armstrong Project][] when we
started instead of BSD, which is more common in the Python community (remember,
community signifiers and all).  I'm not worried about any current contributor,
I'm worried about who might own the work a contributor makes in 1, 2, or 5
years.

Most newspapers are in a state of flux right now.  Let's say *The  Timbuk2
Independent* contributes a few components to Armstrong.  In a few years, they
get bought by MegaNewsProfitExtraction, Inc. who then starts evaluating all of
the intellectual property they've acquired.  They realize the contribution from
*The Independent* is patentable and apply for an receive a patent for their
small contribution.  Under a license like BSD or MIT MNPE, Inc. can now go
around attempting to collect all of the patent licensing fees they're due based
on your use of Armstrong.

I don't think that scenario is that far out there.  Remember, you never write
the rules for the guy you like, you write them for the one you don't.  Assuming
this scenario, the best thing we can all do to protect ourselves is use a
license that protects us from the future patent trolls that are lurking under
the bridges of acquisitions.

Got other ideas?  I'm interested in hearing them.

[alpha-license-list]: http://opensource.org/licenses/alphabetical
[Apache License 2.0]: http://opensource.org/licenses/Apache-2.0
[Armstrong Project]: http://www.armstrongcms.org/
[Backbone.js]: http://backbonejs.org/
[Contributor License Agreements]: http://en.wikipedia.org/wiki/Contributor_License_Agreement
[DocumentCloud]: http://www.documentcloud.org/
[Django]: https://www.djangoproject.com/
[GPLv3]: http://opensource.org/licenses/GPL-3.0
[IANAL]: http://lmgtfy.com/?q=IANAL
[Intellectual Property and Open Source]: http://www.amazon.com/Intellectual-Property-Open-Source-Protecting/dp/0596517963/
[JKM]: http://jacobian.org/
[Linux Kernel]: https://www.kernel.org/
[NICAR]: http://ire.org/conferences/nicar-2013/
[npmjs.org]: https://npmjs.org/
[popular-license]: http://opensource.org/licenses
[PSF]: http://www.python.org/psf/
[Python]: http://python.org/
[Rails]: http://rubyonrails.org/
[SugarCRM's]: http://www.sugarcrm.com/
[Underscore.js]: http://underscorejs.org/
[Van Lindberg]: https://twitter.com/VanL
[WordPress]: http://www.wordpress.org/

