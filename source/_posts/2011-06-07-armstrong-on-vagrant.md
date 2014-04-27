---
layout: post
title: Armstrong on Vagrant
subtitle: Adventures in setting up a virtual machine version of Armstrong
categories:
  - opensource
  - armstrong
---
We released our first version of [Armstrong][] this past Wednesday.  After
taking a quick breather, I set out on getting Armstrong setup inside a
[Vagrant][] virtual machine to make evaluation easy.  I finally got it running.
There's more information about getting started [in the README][AoV README],
where it belongs, but I ran into some interesting technical issues while
setting it up that I want to document here.


## Vagrant + Puppet + pip
I initially wanted to create a full build-script inside Vagrant that could be
used to setup the entire environment.  I used [puppet][] to start the process
and found the [puppet-pip][] provider so I was even going to be able to install
Armstrong easily.  Or so I thought.

There's something that is happening when puppet runs [pip][] that causes the
installation to fail.  I'm a big subscriber to [select not being broken][], but
in this case I think there's some odd in the combination of pip and puppet.
The reason is that `pip install armstrong` via an ssh connection to the same
virtual machine works.  After briefly discussing it on #pip on Freenode, I
opened [ticket #298][] which outlines the issues we ran into.

I finally decided to go the pragmatic route.  For the time being I have a box
that's installed the way you would if you had a raw box yourself.  It's not
ideal, but our new [armstrong box][] (warning, that's a 500mb download) boots
up with everything you need to start playing with Armstrong.

Eventually, either I'll figure out what the issue with pip+puppet is or I'll
switch to some other method that will work.  My reason for picking puppet was
pretty simple.  The [provisioning][Vagrant GS Provisioning] section of the
[getting started][Vagrant Getting Started] guide for Vagrant shows you puppet
code and says essentially "Chef it too complex to simply show you how, so just
use this prepared stuff."  I like simple.  Right or wrong decision, I'm not
100% sure yet.


## Django Server on Bootup
The server runs on startup thanks to [upstart][] in Ubuntu.  As far as Ubuntu
is concerned, Armstrong is now a service that can be started and stopped with
`start armstrong`, `stop armstrong`, and so on.

Upstart works on the concept of events.  Different tasks emit different events
that other tasks can be configured to react to.  There's a `startup` event and
a `net-device-up` event and so on.  I tried all manner of combinations before
it dawned on me, the VM is booting, then Vagrant is mounting the NFS with the
project.

Once I figured that part out, [this recipe][wait_for_file] helped get things
started.  A quick task that starts monitoring for the `config/development.py`
file that is mounted after booting was all I need to get `runserver_plus` going
on "bootup".  You can check out the upstart scripts being used
[in the repository][upstart scripts].

I chose `runserver_plus` from [django-extensions][] rather than the built-in
`runserver` because of [issue 15880][].  Since I'm starting the script on
start up, there's no interactive interface and the watcher gets a little
wonky.  It works out though, because you get the awesome [werkzeug debugger][]
for development.


## Closing
Minus a few oddities in the process, I'm really pleased with the end result.
It should be noted that this is meant for *development only*.  As we near our
first stable release later this year I hope to be able to create another box
that's more deployment ready, but hopefully this will get you started down the
right path.


[AoV README]: https://github.com/armstrongcms/armstrong_on_vagrant#readme
[Armstrong]: http://armstrongcms.org/
[armstrong box]: https://boxes.armstrongcms.org.s3.amazonaws.com/armstrong.box
[django-extensions]: https://github.com/django-extensions/django-extensions
[issue 15880]: https://code.djangoproject.com/ticket/15880
[pip]: http://www.pip-installer.org/
[puppet]: http://www.puppetlabs.com/
[puppet-pip]: https://github.com/rcrowley/puppet-pip
[select not being broken]: http://www.travisswicegood.com/2009/01/04/select-isn-t-broken/
[ticket #298]: https://github.com/pypa/pip/issues/298
[upstart]: http://upstart.ubuntu.com/
[upstart scripts]: https://github.com/armstrongcms/armstrong_on_vagrant/tree/master/upstart
[Vagrant]: http://vagrantup.com/
[Vagrant Getting Started]: http://vagrantup.com/docs/getting-started/index.html
[Vagrant GS Provisioning]: http://vagrantup.com/docs/getting-started/provisioning.html
[wait_for_file]: http://upstart.ubuntu.com/cookbook/#run-a-job-when-a-file-or-directory-is-created-deleted
[werkzeug debugger]: http://werkzeug.pocoo.org/docs/debug/
