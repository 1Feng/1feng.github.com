---
layout: post
title: iTerm2 and xterm-256colors
categories:
  - tools
  - mac
  - tips
---
I'm working on setting up a new (old) MacBook Pro this evening when I came
across an oddity.  Colors in [iTerm2][] were working, but not in
`xterm-256color` mode.  Both laptops are sitting on the table next to each
other, so I started debugging.

There's all manner of bugs that have been reported, but no amount of tweaking
my environment variables worked.  Some time during the process, Xcode 3 finished
installing and all of the sudden things started working.

My guess is that Xcode ships with X11 which is needed for `xterm-256color` to
work, although I'm not planning on uninstalling just to see if it works.  At
any rate, if you're having issues with 256-color mode in iTerm2, check to make
sure you have Xcode installed.

[iTerm2]: http://www.iterm2.com/
