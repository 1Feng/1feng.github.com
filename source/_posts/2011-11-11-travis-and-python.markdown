---
layout: post
title: "Travis and Python"
subtitle: "Subverting Ruby's CI System for Pythonic Purposes"
date: 2011-11-11 15:39
comments: true
categories: 
  - programming
  - opensource
  - armstrong
  - tools
---
Today I took my name back and got Armstrong tests running on [Travis CI][].
Travis CI is the distributed, community run continuous integration server
that the Ruby community has put together.  It lets you do all manner of fun
things, like testing in dozens of different Ruby version configurations.

You're probably wondering what [Armstrong][] is doing there with all of this
talk of Ruby.  No, I didn't rewrite Armstrong in Rails last night.  No, I
didn't convert all of our `fabfiles` over to `Rakefiles` either.  Instead, I
subverted it from within.

{% img right https://img.skitch.com/20111111-cx8i8m8ucpb3862p2g3wy3ags.png %}
Travis CI uses a `.travis.yml` file for all of its configuration.  There are
two key fields that it gives you that let you do fun things with it:
`before_scripts` and `scripts`.

`before_scripts` runs before anything starts.  It's like setup in the xUnit
world, but for your whole environment.  Each of the Armstrong components ships
a `requirements/dev.txt` file, so I tell Travis to do a `pip install -r` of
that during setup.  That's right, Travis CI has [pip][] installed!

Next, I've set the `script` to use our test runner, `fab test` and we're set.
I had to add a few environment variables to turn off our coverage
reports---they don't provide much value when there's no one there to view
them---and we don't need to do a re-install like we do on a local environment.

You can see this in action by checking out the current build status for the
[armstrong.core.arm_wells][] component [here][].  Here's the [.travis.yml][]
file's contents:

    rvm:
       - 1.9.3
    before_script:
      - sudo pip install -r requirements/dev.txt
      - sudo pip install .
    env:
      - SKIP_COVERAGE=1 SKIP_INSTALL=1
    script:
      - fab test
    notifications:
      email: false
      irc:
        - "irc.freenode.net#armstrongcms"

There's work happening to bring native Python support.  Native support means
being able to test against multiple versions and such.  Be sure to check out
the #travis channel on Freenode if you're interested in helping out.

[Travis CI]: http://travis-ci.org/
[Armstrong]: http://armstrongcms.org/
[pip]: http://www.pip-installer.org/
[armstrong.core.arm_wells]: https://github.com/armstrong/armstrong.core.arm_wells
[here]: http://travis-ci.org/#!/armstrong/armstrong.core.arm_wells
[.travis.yml]: https://github.com/armstrong/armstrong.core.arm_wells/blob/ee202a5bfc2a175ea901dcc4456f7db9e67ebc11/.travis.yml
