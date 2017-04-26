<!-- Post Content -->
<article class="container page-container col-lg-8 col-lg-offset-2">
        <div class="row">
            <!-- Page Header -->
            <div class="article-head" style="background-image: url('<?=$img?>')"></div>
            <div class="col-lg-10 col-lg-offset-1">
                <div class="post-heading">
                    <h1><?=$title?></h1><hr>
                    <!--span class="meta">Posted by <a href="#"><?=$author?></a> on <?=date('F j, Y',strtotime($updated))?></span-->
                </div>
                <?=$text?>
            </div>
        </div>
</article>
