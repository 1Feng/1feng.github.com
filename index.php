<?php

function internal_redirect($path) {
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: http://' . $_SERVER['HTTP_HOST'] . $path);
}


$feed_types = array(
    '_atom' => true,
    '_rss2' => true,

    // not sure if these are being used by anyone, but worth having just in case.
    '_rss1' => true,
    '_rss' => true,
);

if (!empty($_GET['tempskin'])) {
    if (isset($feed_types[$_GET['tempskin']])) {
        internal_redirect('/atom/');

        # Doing something devious here - in case a feed reader does not
        # follow redirects, pull the content in.
        echo file_get_contents(dirname(__FILE__) . '/atom/index.html');
        exit();
    }
}

if (empty($_SERVER['PATH_INFO'])) {
    $_SERVER['PATH_INFO'] = '/';
}
# Attempt a redirect to its new home
internal_redirect($_SERVER['PATH_INFO']);
exit();

