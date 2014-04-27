---
layout: post
title: "Using Basketweaver with GitHub"
subtitle: "How to get past the pain of pypi.python.org"
date: 2011-12-21 15:44
comments: true
categories: 
  - opensource
  - python
  - armstrong
  - github
---
Last month I [blogged about using Travis CI][using-travis-ci] with
[Armstrong][].  Things have been going along fine until the last few weeks.
Tests were failing due to network timeouts while talking to [PyPI][].  Never
one to take failing tests lightly, I set out to fix it.

From local testing, it appeared that there was some sort of selective filtering
happening at the server level on PyPI that was causing our tests to fail.  All
of our tests in the CI environment follow these tests:

* Install all of the development requirements with `pip install -r requirements/dev.txt`
* Install the local package
* Execute the tests using `fab test`

I could follow these steps to the letter locally in a fresh virtualenv, but the
second they hit the Travis-CI server they would time out while trying to
install everything.  We've seen similar behavior at the Tribune when we roll
out new servers.  PyPI appears to be up, but installs fail due to timeouts.

Once I confirmed this, I started looking at alternatives to pypi.python.org as
our main index for testing.  My initial thought was to have a dynamic server
that would act as a proxy to PyPI and cache everything locally.  This requires
the least amount of work long-term---assuming the server stays up.  The problem
was that nothing worked quite the way I wanted.  The closest I found was
[collective.eggproxy][].  It felt a little odd and wasn't very configurable
without going the Paster route, so I decided to fall back on [basketweaver][].

Basketweaver builds a static index suitable for using with [pip][] via the
`--index-url` option.  It takes a directory of files, then generates the HTML
that pip can scrape to determine if the package exists.  This HTML can be
hosted anywhere that can serve a static HTML page, such as [GitHub Pages][].


## Working with GitHub

There's a few hoops to jump through when deploying to GitHub Pages.  First,
make sure you include an empty `.nojekyll` file.  GitHub assumes everything you
want to publish is in [Jekyll][], but this file tells GitHub to not parse your
files.

Next, and I can't count the number of times I've done this, GitHub Pages
doesn't give you directory indexes.  Basketweaver generates its index in the
`/index/` directory so you can't hit the plain GitHub Pages URL and expect to
see anything more than an error message.  Make sure to add the `/index/` after
your GitHub Pages URL to view the it once you've published your changes.

The next thing I do is rework where basketweaver looks for files to build the
indexes.  I really don't want to look at a full directory of files at my root
directory, instead I want all of the files stored in the creatively named
`./files/` directory.  Basketweaver installs a file called `makeindex` which I
can never remember, so I created a [run.py][] file that remembers it for me.

The last thing to do is to use the newly created index when installing
packages.  For Armstrong, we do this with:

    pip install -i http://armstrong.github.com/pypi.armstrongcms.org/index/ \
        -r requirements.txt

I haven't gone to the trouble of setting up a `CNAME` for
`pypi.armstrongcms.org` yet, so we're using the main github.com-based address.

There's one final gotcha: PyPI uses routing that treats
[http://pypi.python.org/pypi/South/][south-upper] and
[http://pypi.python.org/pypi/south/][south-lower] as the same URL.  That's why
`pip install Django` and `pip install django` both work even though the former
is the correct package name.  The URL spec is ambigous as to whether this is
correct, but most web servers are case sensitive, including GitHub Pages.

This will get you if you have dependencies on packages that don't use all
lowercase names, such as South, Fabric, or Django.  All three of these are
dependencies of Armstrong.  The fix is to make sure that your
`install_requires` and requirements files have the correct case.  The easiest
way to determine this is to look at the output of `pip freeze` and make sure
you're using the same package name as it generates.


## Conclusion

At the end of the day, this keeps our tests from being held hostage whenever
PyPI goes on the fritz or starts randomly filtering requests as it seemed to do
this past week.  All that said, we're still borrowing other people's
infrastructure.  GitHub had a little blip while I was writing this post,
underlining that you get what you pay for.

While you can use Basketweaver and GitHub to create a mirror of sorts for your
packages, make sure you control the infrastructure if its mission critial that
everything always stay up.  That, or pay for it so there's someone to call when
it goes down.


[using-travis-ci]: http://www.travisswicegood.com/2011/11/11/travis-and-python/
[Armstrong]: http://www.armstrongcms.org/
[PyPI]: http://pypi.python.org/pypi
[collective.eggproxy]: http://pypi.python.org/pypi/collective.eggproxy/0.5.1
[basketweaver]: http://pypi.python.org/pypi/basketweaver/
[pip]: http://www.pip-installer.org/
[GitHub Pages]: http://pages.github.com/
[Jekyll]: http://jekyllrb.com/
[run.py]: https://github.com/armstrong/pypi.armstrongcms.org/blob/gh-pages/run.py
[south-upper]: http://pypi.python.org/pypi/South/
[south-lower]: http://pypi.python.org/pypi/south/
