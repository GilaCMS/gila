<?php
global $db;
$posts = $db->get("SELECT id,title,SUBSTRING(post,1,300) as post,
    (SELECT value FROM postmeta WHERE post_id=post.id AND vartype='thumbnail') as img
    FROM post ORDER BY id DESC LIMIT 5");
?>

<div class="fashion">
  <div class="single_post_content">
    <h2><span>Fashion</span></h2>
    <ul class="business_catgnav wow fadeInDown">
      <?php $r = $posts[0]; ?>
      <li>
        <figure class="bsbig_fig"> <a href="<?=$r['id']?>" class="featured_img"> <img alt="" src="<?=$r['img']?>"> <span class="overlay"></span> </a>
            <figcaption> <a href="<?=$r['id']?>"><?=$r['title']?></a> </figcaption>
            <p><?=nl2br(strip_tags($r['post']))?>...</p>
        </figure>
      </li>
    </ul>
    <ul class="spost_nav">
<?php for ($i=1;$i<count($posts);$i++) { $r = $posts[$i]; ?>
        <li>
            <div class="media wow fadeInDown"> <a href="<?=$r['id']?>" class="media-left"> <img alt="" src="<?=$r['img']?>"> </a>
                <div class="media-body"> <a href="<?=$r['id']?>" class="catg_title"> <?=$r['title']?></a> </div>
            </div>
        </li>
<?php } ?>
    </ul>
  </div>
</div>
