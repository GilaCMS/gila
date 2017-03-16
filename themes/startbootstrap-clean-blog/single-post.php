<!-- Page Header -->
<!-- Set your background image for this header on the line below. -->
<header class="intro-header" style="background-color:#333; background-image: url('<?=$img?>')">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                <div class="post-heading">
                    <h1><?=$title?></h1>
                    <!--h2 class="subheading">Problems look mighty small from 150 miles up</h2-->
                    <span class="meta">Posted by <a href="#"><?=$author?></a> on <?=date('F j, Y',strtotime($updated))?></span>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Post Content -->
<article>
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                <?=$text?>
            </div>
        </div>
    </div>
</article>
