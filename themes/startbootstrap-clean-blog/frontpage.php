<!-- Page Header -->
<!-- Set your background image for this header on the line below. -->
<header class="intro-header" style="background-color:#333; background-image: url('themes/startbootstrap-clean-blog/img/home-bg.jpg')">
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
</header>

<!-- Main Content -->
<div class="container">
    <div class="row">
        <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
            <?php foreach (blog::post() as $r) { ?>
            <div class="post-preview">
                <a href="<?=$r['id']?>">
                    <h2 class="post-title"><?=$r['title']?></h2>
                    <h3 class="post-subtitle"><?=nl2br(strip_tags($r['post']))?></h3>
                </a>
                <p class="post-meta">Posted by <a href="#">Start Bootstrap</a> on September 24, 2014</p>
            </div>
            <hr>
            <?php } ?>
            <!-- Pager -->
            <ul class="pager">
                <li class="next">
                    <a href="#">Older Posts &rarr;</a>
                </li>
            </ul>
        </div>
    </div>
</div>
