---
layout: post
title: "Past, Present, and Future of Armstrong"
date: 2013-06-03 21:21
comments: true
categories: 
  - armstrong
  - opensource
  - django
  - python
---
Most of you who know me have heard me talk about [Armstrong][], the open-source news platform that I helped create when I first joined the [Texas Tribune][].  I have and continue to talk at length about Armstrong and its future, but I've never collected those thoughts into one cohesive document outlining how we got to where we are now, what the current state of the project is, and where I hope to see it go.

This post is my attempt to do that.

## Past
Armstrong is peculiar if you look at it from the outside.  It might be hard to understand exactly what it is and how it got to the point it is.  This section should help you understand that a bit more.

### Not a CMS
Using common names can be unfortunate.  Most people hear Armstrong CMS and think it's a content management system akin to [Eidos][] on the closed-source side or [Wordpress][] and [Drupal][] on the open-source side.  I've always envisioned Armstrong as a platform to build on top of, not simply a CMS.  The distinction is small, but important.

*You work within a CMS*

*You build on top of a platform*


A CMS is something you use.  It provides the tools you need to manage content.  A platform is something that provides a base to build upon.  It's my belief that more 95% (maybe more) of the pieces that make a news focused website a news website are all the same.  Everyone needs a [way to collect similar content into sections][sections], a [way to schedule content][wells], or a [way to control publication status][pub-mixins] on content.  That last 5% of content, however, is unique and what makes a site interesting.  Being able to reuse data about [bills][bill-tracker] [throughout][bills-ex-1] [your][bills-ex-2] [site][bills-ex-3] without having to confine it to a [big blob of text][holovaty] is what allows us to do interesting things with a site.  This is where Armstrong comes in.

Armstrong isn't meant to be used out-of-the-box any more than a Lego&trade; train set is meant to be a toy train set as soon as you unpack it.  Both require assembly, but both allow you to exercise some creativity in how you assemble them.  To do this, we've taken an unconventional approach to packaging everything in the project.

### Everything is a package
Python is famous for it's horrible packaging solutions.  It's gotten a lot better over the last few years, but people still package most software in one big bundle.  I created the packaging schema for Armstrong based on the way I wished Python packages were handled, as if they followed an adopted version of the Unix Philosophy:

> Packages should do one thing, and do them well.  Packages should work together.

This means that there's some 25 packages on [PyPI][] for Armstrong.  Many of these work in concert with other Armstrong packages to form a bigger whole.  I broke it down along two main lines with a few others thrown in.

#### `armstrong.core`
This section of Armstrong contains all of the pieces essential to nearly *all* websites.  These packages either have *no* models, or are meant to be used almost exactly as-is with little or no modification.

All core packages contain an `arm_` prefix in the last part of their name: `armstrong.core.arm_content`, `armstrong.core.arm_wells`, and so on.  This is to avoid potential naming conflicts since Django still assumes that its apps are all flat without full module names.

Try as I might, `armstrong.core.arm_content` did end up pretty big.  It contains most of the [mixins][] used to build the larger models throughout the system.  Anything that can loosely  be considered content in an Armstrong project probably has some connection to this package.

#### `armstrong.apps`
These apps are meant to be usable out-of-the-box, but are most useful as example implementations.  This is one area that I didn't document as well as I should have.  Almost no one would (or should, for that matter) use `armstrong.apps.articles` as their out-of-the-box article implementation.  For very simple sites it will probably work, but my assumption has always been that most sites will take that as a guide and build something similar to it.

A great example of this is the `armstrong.apps.donations` project.  We use that pretty extensively at the Texas Tribune since we're a member-driven organization, but we don't use it in its out-of-the-box configuration.  We have a custom `tt_donations` app that extends the [views][armstrong.apps.donations.views] to add extra functionality and we have custom [backends][armstrong.apps.donations.backends] for all of our payment processing and CRM syncing.

#### Hatband and Pops
Any news tool is only as good as it's admin interface.  Unfortunately, most of our time while funded by the Knight Foundation was spent on backend code, but we do have a solid start of a custom admin interface that's broken down into two pieces.

[Hatband][armstrong.hatband] is the collection of Armstrong-specific interfaces to the Django admin.  It's meant to be used as a drop-in replacement for `django.contrib.admin` that extends the behavior.  It provides several custom inline interfaces and will hopefully contain even more.  It exposes a [JSON API][armstrong.hatband.views.modelsearch] for searching any type of model that's registered with the admin and has [`search_fields`][django-search_fields] turned on.  The plan has always been to use this to create a rich API on top of the admin.  Search is simply the first foray into that.

Where Hatband is behind-the-scenes, [Pops][] is the user-interface side.  It's currently built using Twitter's [Bootstrap][] framework and was built on top of a fork of [django-admintools-bootstrap][].  Pops is meant to be entirely standalone and have no ties to Armstrong at all since it's simply the skin on the interface.

## Current State
The original Knight Foundation grant to The Bay Citizen and The Texas Tribune ended in early summer of 2012.  Since that time there hasn't been any full-time development dedicated to Armstrong, but that's not to say that development has stopped.  Both the BC and TT, along with a handful of other organizations, use Armstrong internally and are continuing development on the project.

There are a few things I want to call out.

### Timed Releases of Armstrong
The original idea, and one I've deviated from since last year, was to do timed releases, every three month.  You might have noticed that [the version numbers are pretty high][armstrong-tags].  That's because they follow the format `vYY.MM`.  That way you know when the last major release of Armstrong was.  Each one of those releases is just the latest stable code from all of the components of Armstrong that are considered production ready.

One key point, however, is that the main `armstrong` package (note the lowercase, that means it is code in its packaged state, not the project as a whole), is just a collection of other packages.  You don't have to install `armstrong` to be able to use various components.  For example, you can pull [armstrong.utils.backends][] into any project without using anything else from Armstrong if that's all you need.

### Release Components
All of the components of Armstrong are released independent of the major `armstrong` releases.  There's been a handful of component releases in the last year and more are being worked on right now.  Each of these follows [Semantic Versioning][SemVer], or SemVer.  That means that you can always upgrade within a major version, say from v1.2 to v1.6, and not worry about your code breaking.  If anything breaks, a new major number is introduced.  So far, we've only had to do that once: [armstrong.apps.related_content][].

All of the components in Armstrong that reach a v1.0.0 release, other than those grandfathered in for armstrong's first stable release (v11.09), are being used in production.  Following SemVer, production code goes to v1.0.0 as soon as it's production.  Part of the criteria for code making it to stable is that it's being used on at least one site as production code.  There's one `v0.x` release that has running code so you can install it, then once that's ready the `v1.0.0` should be a simple version number bump with no code change.

The production requirement goes for point releases of code once it goes stable as well.  Someone has to be using the code that's in a pull release in order for it to be considered stable (and tested) enough for a release.  Unit tests are a requirement, but code that is running on production is the final requirement for any component that's released as stable.

This points the burden mainly on the Bay Citizen and Texas Tribune to make sure we're running code that we're trying to get released.  Right now we're the main ones that are effected by this, but it allows us to ensure the quality of the code.  As more organizations start to contribute, they'll have to play by the same rules.

## Future Plans
There are a handful of areas where I would like to see Armstrong grow toward in the future.  These coincide with the technical direction here at the Texas Tribune, so we'll be driving many of these changes based on restructuring of our internal code.

### Streams of Content
One of the biggest regrets in Armstrong was that I relented when arguing that we should structure content to exist independently with streams that any content could opt-in to.  The argument against this route was that we had a tried solution---monolithic concrete-model dependencies---so why try something new until we'd replicated what we knew works.  The old method does work, but it's not scalable, whereas independent streams that you push content in to means you can decouple that relationship and scale it to many different types of content.

For example, say you have an Education Stream that contains content related to education.  Stories can put themselves in that stream by providing the information the stream expects, but so can an update to data about a campus.  All the data has to do is be able to render itself the way the stream expects and it can opt-in.

My initial plan is that an object would provide at least one rendered version of itself and its canonical URL.  For a section stream, that rendered version would probably be the title, plus artwork, byline, and description.

This decentralization means that the stream display can be moved around to different services.  You could also make it streams all the way down.  Content notifies data streams that expect certain JSON documents and those data streams notify content destination streams (think: HTML, iOS, computers on the dashboard, TVs, and so on and so forth).

Those with a background in enterprise software might recognize this type of decentralization by another name: Service Oriented Architecture, or SOA.  This type of architecture is not simply nice, it's a requirement in a multi-device world.  Building services that can only return HTML is shortsighted and going to cause problems as the number of devices our content is displayed on explodes in the coming years.  Decoupling content from the various streams they're consumed in is the first step in future proofing Armstrong.

### Testbed for a new Django Admin
One area where I think a SOA allows greater freedom is the admin interface.  The Django admin is great for what it gets you out-of-the-box, but you outgrow it very quickly especially when it's laid next to modern web tools.  You have to remember, the Django admin was designed in 2004/2005 when your main option for dealing with any type of data was phpMyAdmin and editing the database directly!

One thing I hope to do with the Hatband/Pops combo is create a testbed for experimenting on top of Django's admin.  These roles aren't set in stone, but my thought is that Hatband will serve as the place for Armstrong-specific experimentation and Pops will be the place for generic Django experimentation.

Since starting Hatband and Pops, a few other tools have popped up in this space.  Nobody has gotten significant traction, but I'm not opposed to joining forces with one of them if somebody does start to head down a solid path, but there are a few things that I see as a requirement.

1. It needs to build on top of the existing `django.contrib.admin` code.  The admin's bones (with a few exceptions) are really solid.  Rewriting it from the ground up isn't a good use of time.  It needs a lot of refactoring and many of the changes wouldn't be backwards compatible, but it's possible to make it happen by simply building on top of what already exists.

2. It needs to focus on decoupling the actual interface from the discovery and registration of apps along with exposing an API to them.  Right now, the biggest wart on the Admin is how tightly coupled display and discovery are.  Any new admin needs to focus on providing a solid API (both Python and REST) for working with the apps that are registered.  On top of that you can build a solid, default client interface.  Having a [dogfooded][] API ensures that others can build alternative interfaces on top of it.  Think: native iOS apps for the admin!

3. It needs to exist outside of core Django.  I think the Admin is one of the reasons Django has gotten the traction it has.  That's great, but right now it moves too slowly for that to be useful for such a potentially rich web application.  Having the admin exist as a separate project means that it can move more nimbly, release more often if it needs to, and gather support from those who might have no interest in working on a traditional web framework, but would love to work on a web application.

### Separating Editing and Publishing
Currently, CMS tools assume that you're editing and publishing content in the same tool.  Those are two different workflows that need to have different tools: a collaborative authoring/editing tool and a publishing tool.  They can exist on the same domain and even within the same major tool, but each workflow needs a different presentation and to be separated from the other.

The editing tool needs to have real-time feedback for the user when edits are happening.  It should update in real-time showing who is editing what, the changes they've made, and so forth.  It could include the ability to lock a field by focusing on it, but it should allow you to return control back to other users by removing focus, but ideally it's smart enough to work with multiple users editing the same content.

This interface needs to focus the user on writing and editing.  Things like sections, tags, locations, and so forth are all secondary content that should be tucked away, accessibly only when called on, to allow the user to focus on the task at hand.  It would take a lot of user testing to design this system in a way that it could replace the existing tools (the number of emailed Word documents at the Tribune is still a source of embarrassment for me), but you'd end up with a solid workflow to take something from idea to finished product with the right focus.

Once the content has been written and edited, it needs to be published.  Focusing explicitly on content takes all layout decisions away from the authoring experience and moves them to a place where you can make device-specific choices.

Responsive design should be the first choice for all content so it reaches the broadest audience, but there is also room for device-specific display where it make sense.  Layout tools should enable this.

There is a start for this in [armstrong.core.arm_layout][] with some code to help reuse model-specific rendering throughout the site, but those are baby steps toward a GUI-based layout tool.  

Being able to control any aspect of the display of the site across multiple devices is the Holy Grail of a news platform, and one I hope we're able to tackle as part of the Armstrong community.

## Up Next
There are some very immediate plans, however.  First, we need to roll another release of `armstrong`.  I plan on creating `armstrong v13.06` at the end of the month with the latest stable versions of all of the stable components.

`v13.06` is going to be a maintenance release to get everything updated to the latest and remove the component-level requirement on Django and ensure full testing of Django from Django 1.3.x through Django 1.5.x.  From here forward, the only dependency on Django is going to be specified at the `armstrong` level, leaving it up to developers to work through whether they can upgrade.  We'll continue to test components against all supported versions of Django.

This release will put us back on the timed release.  The next release after this will be `v13.09`.  I know the folks over at the old Bay Citizen have some new code they would like to see released and I'm hoping to have a solid admin interface for [armstrong.apps.related_content][] by then as well.


[Armstrong]: http://armstrongcms.org/
[armstrong.apps.donations.backends]: https://github.com/armstrong/armstrong.apps.donations/blob/master/armstrong/apps/donations/backends.py
[armstrong.apps.donations.views]: https://github.com/armstrong/armstrong.apps.donations/blob/master/armstrong/apps/donations/views.py
[armstrong.apps.related_content]: https://github.com/armstrong/armstrong.apps.related_content/
[armstrong.core.arm_layout]: https://github.com/armstrong/armstrong.core.arm_layout
[armstrong.hatband]: https://github.com/armstrong/armstrong.hatband/
[armstrong.hatband.views.modelsearch]: https://github.com/armstrong/armstrong.hatband/blob/master/armstrong/hatband/views/search.py#L81-L92
[armstrong.utils.backends]: https://github.com/armstrong/armstrong.utils.backends/
[armstrong-tags]: http://github.com/armstrong/armstrong/tags/
[bills-ex-1]: http://www.texastribune.org/session/83R/bills/sent-to-governors-office/
[bills-ex-2]: http://www.texastribune.org/session/83R/bills/house/failed/
[bills-ex-3]: http://www.texastribune.org/session/83R/bills/trackers/education-reform-bills/
[bill-tracker]: http://www.texastribune.org/session/83R/bills/
[Bootstrap]: http://twitter.github.io/bootstrap/
[django-admintools-bootstrap]: https://bitbucket.org/salvator/django-admintools-bootstrap
[django-search_fields]: https://docs.djangoproject.com/en/1.5/ref/contrib/admin/#django.contrib.admin.ModelAdmin.search_fields
[dogfooded]: http://en.wikipedia.org/wiki/Eating_your_own_dog_food
[Drupal]: https://drupal.org/
[Eidos]: http://www.eidosmedia.com/
[holovaty]: http://www.holovaty.com/writing/fundamental-change/
[mixins]: https://github.com/armstrong/armstrong.core.arm_content/tree/master/armstrong/core/arm_content/mixins
[Pops]: https://github.com/tswicegood/pops/
[pub-mixins]: https://github.com/armstrong/armstrong.core.arm_content/blob/master/armstrong/core/arm_content/mixins/publication.py
[PyPI]: https://pypi.python.org/pypi?%3Aaction=search&term=armstrong&submit=search
[sections]: https://github.com/armstrong/armstrong.core.arm_sections
[SemVer]: http://semver.org/
[Texas Tribune]: http://www.texastribune.org/
[wells]: https://github.com/armstrong/armstrong.core.arm_wells
[Wordpress]: http://wordpress.org/
