---
layout: post
title: "Elegantly Simple"
subtitle: "Why first-class functions matter"
date: 2011-10-23 22:16
comments: true
categories: 
  - programming
  - javascript
  - node
---
JavaScript catches a lot of flack for it's "ugliness," but I'm rather fond of
the language.  It's first-class functions make up for any quirks you have to
deal with in the language.  Consider this test case:

{% gist 1308316 output.js %}

It generates this output when run with `--spec`:

{% gist 1308316 output.txt %}

I'm using test cases like this throughout my upcoming Programming Node.js book
to test output of some of the simple scripts.

Yes, I know you can get some amazingly expressive test cases in other
languages, but I dare people who say that JavaScript is any ugly language to
find fault with this bit of code.
