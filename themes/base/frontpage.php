<!-- Page Header -->
<!--header class="intro-header" style="background-color:#333; background-image: url('themes/startbootstrap-clean-blog/img/home-bg.jpg')">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                <div class="site-heading">
                    <h1><?=gila::config('title')?></h1>
                    <hr class="small">
                    <span class="subheading"><?=gila::config('slogan')?></span>
                </div>
            </div>
        </div>
    </div>
</header-->

<!-- Posts -->
<div class="wrapper" style="background:#e8e8e8">
    <?php foreach (blog::post() as $r) { ?>
    <div class="bordered wrapper margin-16px row" style="background:white">
        <div class="gm-2">
            <img src="<?=view::thumb($r['img'],$r['id'].'_sm.jpg',180)?>" style=" max-width:100%; height:auto">
        </div>
        <div class="gm-10" style="padding-left:20px">
            <a href="<?=$r['id']?>">
                <h3 class="post-title" style="margin-top:0"><?=$r['title']?></h3>
            </a>
            <?=nl2br(strip_tags($r['post']))?>
        </div>
    </div>
    <?php } ?>
    <!-- Pagination -->
    <ul class="g-nav">
        <li class="">
            <a href="?page=<?=$page+1?>">Older Posts &rarr;</a>
        </li>
    </ul>
</div>
