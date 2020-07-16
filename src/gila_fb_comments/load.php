<?php

Event::listen('post.after', function () {
  $language = Config::option('gila_fb_comments.language');
  $appID = Config::option('gila_fb_comments.appID');
  $href = Config::config('base').Router::$url; ?>
  <div id="fb-root"></div>
  <script async defer crossorigin="anonymous" src="https://connect.facebook.net/<?=$language?>/sdk.js#xfbml=1&version=v5.0&appId=<?=$appID?>&autoLogAppEvents=1"></script>
  <div class="fb-comments" data-href="<?=$href?>" data-numposts="5"></div><?php
});
