<section id="sliderSection">
  <div class="row">
      
    <div class="col-lg-8 col-md-8 col-sm-8">
      <div class="slick_slider">
<?php
global $db;
$posts = $db->get("SELECT id,title,SUBSTRING(post,1,120) as post,(SELECT value FROM postmeta WHERE post_id=post.id AND vartype='thumbnail') as img FROM post ORDER BY id DESC LIMIT 4");
foreach ($posts as $r) {
?>
        <div class="single_iteam"> <a href="<?=$r['id']?>"> <img src="<?=$r['img']?>" alt=""></a>
            <div class="slider_article">
            <h2><a class="slider_tittle" href="<?=$r['id']?>"><?=$r['title']?></a></h2>
            <p><?=nl2br(strip_tags($r['post']))?>...</p>
            </div>
        </div>
<?php } ?>
      </div>
    </div>

    <div class="col-lg-4 col-md-4 col-sm-4">
      <div class="latest_post">
        <h2><span>Latest post</span></h2>
        <div class="latest_post_container">
          <div id="prev-button"><i class="fa fa-chevron-up"></i></div>
          <ul class="latest_postnav">
<?php
foreach ($posts as $r) { ?>
            <li>
                <div class="media"> <a href="<?=$r['id']?>" class="media-left"> <img alt="" src="<?=$r['img']?>"> </a>
                <div class="media-body"> <a href="<?=$r['id']?>" class="catg_title"> <?=$r['title']?></a> </div>
                </div>
            </li>
<?php } ?>
          </ul>
          <div id="next-button"><i class="fa  fa-chevron-down"></i></div>
        </div>
      </div>
    </div>

  </div>
</section>
