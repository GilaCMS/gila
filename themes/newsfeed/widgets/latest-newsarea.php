<?php
global $db;
$posts = $db->get("SELECT id,title,
    (SELECT value FROM postmeta WHERE post_id=post.id AND vartype='thumbnail') as img
    FROM post ORDER BY id DESC LIMIT 9");
?>

<div class="latest_newsarea"> <span>Latest News</span>

  <ul id="ticker01" class="news_sticker">
    <?php foreach ($posts as $r) { ?>
    <li><a href="<?=$r['id']?>"><img src="<?=$r['img']?>" alt=""><?=$r['title']?></a></li>
    <?php } ?>
  </ul>
  <div class="social_area">
    <ul class="social_nav">
      <li class="facebook"><a href="#"></a></li>
      <li class="twitter"><a href="#"></a></li>
      <li class="flickr"><a href="#"></a></li>
      <li class="pinterest"><a href="#"></a></li>
      <li class="googleplus"><a href="#"></a></li>
      <li class="vimeo"><a href="#"></a></li>
      <li class="youtube"><a href="#"></a></li>
      <li class="mail"><a href="#"></a></li>
    </ul>
  </div>
</div>
