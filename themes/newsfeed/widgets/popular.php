<div class="single_sidebar">
  <h2><span>Popular Post</span></h2>
  <ul class="spost_nav">
<?php
global $db;
$res = $db->query("SELECT id,title,(SELECT value FROM postmeta WHERE post_id=post.id AND vartype='thumbnail') as img FROM post ORDER BY id DESC LIMIT 5");
while ($r = mysqli_fetch_array($res)) { ?>
    <li>
      <div class="media wow fadeInDown"> <a href="<?=$r['id']?>" class="media-left"> <img alt="" src="<?=$r['img']?>"> </a>
        <div class="media-body"> <a href="<?=$r['id']?>" class="catg_title"> <?=$r['title']?></a> </div>
      </div>
    </li>
<?php } ?>
  </ul>
</div>
