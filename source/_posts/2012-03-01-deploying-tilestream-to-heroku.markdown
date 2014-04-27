---
layout: post
title: "Deploying TileStream to Heroku"
date: 2012-03-01 13:14
comments: true
categories: 
---
This past week I attend the 2012 IRE conference.  Remember all of those #nicar12 tweets you saw from me and few other programmery/journalisty type people?  That's the conference we were all hanging out at.

Custom maps were one of the big themes.  There were a few [TileMill] talks and they were all packed.  TileMill, for those who aren't familiar, is a tool that let's you create custom map tiles–the images that make up maps like Google Maps–so you can have a map that's entirely unique.  An example of this is the [Idaho Unemployment Map] by the folks over at [State Impact].

We've been talking about using TileMill at the [Texas Tribune] for months now, but we've yet to actually deploy one.  A few of us have TileMill locally and have played with it, but the tile serving component is something we haven't touched.

I came back from the conference and got sick.  Yesterday, while trying to kill some time without thinking of anything particularly important I decided to see what was involved with deploying [TileStream].

TileStream is a tile server written in Node by [MapBox], the creators of TileMill, to generate and server the tiles for a map you create.  Since tiles are simply PNGs, it seems like you should be able to just generate a whole host of files, upload them to a server, and call it a day.  The problem that a tile server solves is having to generate *all* of those tiles at once.  Generating them, then uploading them once is a pain, but what happens if you need to make a change to them?

Lately, I've been on a "no new servers" kick.  I'm tired of seeing the amount of time spent tweaking servers instead of working on code.  DevOps is fun, don't get me wrong, but sysadmins we are not.  With that in mind, I decided to take a look at what's involved in deploying TileStream to [Heroku], a "cloud application platform" that supports a whole host of languages---including Node.

## Preparing for Deploy
The very first thing you have to do is create a map an export it.  That's a topic unto itself, so I'm not going to cover it here.  I created a simple copy of the state of Texas with all of its counties outlined and colored in.  I forget where I procured the shape file, but some Googling should turn it up if you want to follow along.

Make sure to export the file as the `mbtiles` format when you export it.  Where you export it to isn't important right now, just remember where it's at.

Next, you have to make sure Heroku is installed.  If you already have a working Ruby and gems environment with Git and so installed, you can run `gem install heroku` to get the command line client.  If you don't, check out the [Heroku Toolbelt] for a quick start to get setup.  Once you have the command line tools setup, log in to your Heroku account with `heroku login` and follow the directions.

The next step is to create a new Git repository.  Heroku uses Git as its means of tracking files to deploy.  You're going to have to learn at least a little bit of Git if you're going to use Heroku (side note: I've written [two][pvcgit] [books] on Git and highly recommend [Pragmatic Version Control using Git][pvcgit] if you're new to version control).  Once you have a Git repository, run the command `heroku create -s cedar` inside your working tree.  You should see something similar to this:

    prompt> heroku create -s cedar
    Creating hollow-fire-2448... done, stack is cedar
    http://hollow-fire-2448.herokuapp.com/ | git@heroku.com:hollow-fire-2448.git
    Git remote heroku added

`hollow-fire-2448` is the name of my Heroku application.  Yours will be different.  Now you have to tell Heroku what to install.  To do that for Node applications, Heroku uses a `package.json` file.  That's the file that Node applications use to set up the dependencies to make sure that everything is installed.  For this server, you just need to declare a simple dependency on `tilestream`.  My `package.json` file looks like this:

    {
      "name": "texas-counties",
      "version": "0.0.1",
      "dependencies": {
        "tilestream": "1.0.0"
      }
    }

Add that to the repository using `git add` followed by `git commit`.  The next step is telling Heroku how to run TileStream.  Heroku uses a `Procfile` to handle starting and stopping applications.  The `Procfile` is run using [Foreman] and can define all of the processes required to run an application.  The format is `<name>: <command>` and for this application you only need to add one line:

    web: tilestream --host hollow-fire-2448.herokuapp.com --uiPort=$PORT --tilePort=$PORT --tiles=./tiles

There's a couple of things going on there.  First, notice that I'm explicitly adding a `--host` name and using the name of the app that Heroku told me when I called `heroku create` earlier.  TileStream currently only responds to requests on hosts that it recognizes.  You're going to need to change that line to be whatever your Heroku app's name is.

Next, notice that both `--uiPort` and `--tilePort` are set to the value of `$PORT`.  Heroku exposes `$PORT` as an environment variable to let your application know what port to listen to for incoming connections.

Finally, you set the directory for tiles to `./tiles`.  Commit this, then push to Heroku to verify that everything went according to plan.

    prompt> git push heroku master 
    Counting objects: 6, done.
    Delta compression using up to 2 threads.
    Compressing objects: 100% (5/5), done.
    Writing objects: 100% (6/6), 659 bytes, done.
    Total 6 (delta 0), reused 0 (delta 0)
    
    -----> Heroku receiving push
    -----> Node.js app detected
    … and a whole bunch more output …

Go ahead and stand up and stretch.  Go grab some coffee or tea or water, whatever you vice.  This step takes a few minutes while Heroku installs all of the dependencies and such for TileStream for the first time.  It's kind of awesome, though.  Without a single bit of server administration, you're just a few minutes away from having a fully operational TileStream server.

… waiting on Heroku to finish up …

Ok, done?  Now run `heroku open`.  This launches your browser and opens the URL of the Heroku application.  If everything went well, you should see the empty TileStream server like this.

<div class="thumbnail"><a href="https://skitch.com/tswicegood/8gjk7/tilestream"><img style="max-width:638px" src="/images/tilestream/empty.jpg" alt="Empty TileStream" /></a></div>

If you don't get a page like the above, check the logs by running `heroku logs` to see if it gives you any clues.  Another thing to double check is the process list.  Run `heroku ps` to make sure that `web.1` has a state of up.

That big error is the non-user-friendly way of saying there's nothing in the tiles directory to read and display.  Remember the `mbtiles` file you created earlier?  Now it's time to move it into place.  Inside your Git repository, create a directory called `tiles` and copy the `mbtiles` file into it.  Once the file is in place, add it to Git, then push the new commit to Heroku.

This push is going to take a little bit, depending on how fast your connection is.  It has to send the entire `mbtiles` file over the wire to Heroku.  Having done this a few times now, it seems like Heroku might throttle large uploads.  I start out at a few hundred KB/sec, then it drops down to around 100KB/sec for about 30 seconds before settling in at 80KB/sec.  Their business isn't receiving huge files, so it would make sense if Heroku did throttle to make sure one large upload didn't take over their entire pipe.

Once the push has finished, reload your browser window and you should see your new map, much like this:

<div class="thumbnail"><a href="https://skitch.com/tswicegood/8gjtr/tilestream"><img style="max-width:638px" src="/images/tilestream/one-map.jpg" alt="TileStream with one map" /></a></div>

And now, you have a tile server.  Deploying to Heroku for this is a great fit for the standard news application.  You need the ability to handle tons of traffic as you launch, then scale back until it hits maintenance mode where you only need a skeleton server running.

Heroku gives you one dyno–think of that as one process on a server–for free, with each additional dyno costing $0.05/hour (see the Heroku [pricing] page).  That means you can spin up several dynos to handle the initial flood of traffic, then scale back to a smaller set and only have to pay for the initial spike.  All, without any additional work on your end setting up or configuring servers.

Now, the one caveat to all of this is that I haven't actually tried running TileStream like this with a production load.  I'm not sure what kind of performance we could get out of it or what limitations there might be.  The only way to answer that is to try.  Hopefully we'll be able to pluck one of the projects out of our pipeline and do some custom maps for it using TileMill and TileStream.

## Where to from here?
The next thing you need to do is write some JavaScript to interact with the tile server.  [Leaflet] has gained a lot of popularity and seems to be the default choice.  I've yet to play around with, but that's a topic for another blog post.

If you're interested in seeing what all of the pieces look like together, my Heroku app is still online at [hollow-fire-2448.herokuapp.com].  I'll try to leave it spinning, but if I take it down, I've posted the [repository on GitHub] so you can see all of the files in their original state.


[books]: http://pragprog.com/book/pg_git/pragmatic-guide-to-git
[Foreman]: http://ddollar.github.com/foreman/
[Heroku]: http://www.heroku.com/
[Heroku Toolbelt]: https://toolbelt.herokuapp.com/
[hollow-fire-2448.herokuapp.com]: http://hollow-fire-2448.herokuapp.com/
[Leaflet]: http://leaflet.cloudmade.com/
[MapBox]: http://mapbox.com/
[pricing]: http://www.heroku.com/pricing
[pvcgit]: http://pragprog.com/book/tsgit/pragmatic-version-control-using-git
[repository on GitHub]: https://github.com/tswicegood/tilestream-on-heroku
[State Impact]: http://stateimpact.npr.org/
[Texas Tribune]: http://www.texastribune.org/
[TileMill]: http://mapbox.com/tilemill/
[TileStream]: https://github.com/mapbox/tilestream
[Idaho Unemployment Map]: http://stateimpact.npr.org/maps/idaho/unemployment/
