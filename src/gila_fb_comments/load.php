<?php

event::listen('body',function(){
  $language = gila::option('gila_fb_comments.language');
  $appID = gila::option('gila_fb_comments.appID');
  ?><div id="fb-root"></div>
  <script>(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/<?=$language?>/sdk.js#xfbml=1&version=v2.10&appId=<?=$appID?>";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));</script><?php
});


event::listen('post.after',function(){
  $href = gila::config('base').router::$url;
  ?><div class="fb-comments" data-href="<?=$href?>" data-numposts="5"></div><?php
});
