<?php
global $db;
$posts = $db->get("SELECT id,title,
    (SELECT value FROM postmeta WHERE post_id=post.id AND vartype='thumbnail') as img
    FROM post ORDER BY id DESC LIMIT 6");
?>

<div class="single_post_content">
  <h2><span>Photography</span></h2>
  <ul class="photograph_nav  wow fadeInDown">
      <?php for ($i=0;$i<count($posts);$i++) { $r = $posts[$i]; ?>
          <li>
            <div class="photo_grid">
              <figure class="effect-layla"> <a class="fancybox-buttons" data-fancybox-group="button" href="<?=$r['id']?>" title="Photography Ttile 2"> <img src="<?=$r['img']?>" alt=""/> </a> </figure>
            </div>
          </li>
      <?php } ?>
  </ul>
</div>
