
<!-- Posts -->
<div class="row wrapper">
    <div class="gm-9">
    <?php foreach ($c->posts as $r) { ?>
    <div class="row gap-8px post-review">
            <?php
            if($img=view::thumb_sm($r['img'],$r['id'].'__sm.jpg')){
		       $title_gl='gs-9';
		       echo '<div class="gs-3">';
               echo '<img class="lazy" data-src="'.$img.'" style="width:100%; height:auto">';
		       echo '</div>';
            } else $title_gl='gm-12';
            ?>

        <div class="<?=$title_gl?>">
            <a href="<?=blog::get_url($r['id'],$r['slug'])?>">
                <h2 class="post-title" style="margin-top:0"><?=$r['title']?></h2>
            </a>
            <?=strip_tags($r['post'])?>
        </div>
    </div><!--hr-->
    <?php } ?>
    <!-- Pagination -->
    <?php view::renderFile('pagination.php')?>
    </div>
    <div class="gm-3 sidebar">
      <form method="get" class="inline-flex" action="<?=gila::base_url('blog')?>">
        <input name='search' class="g-input fullwidth" value="<?=(isset($search)?$search:'')?>">
        <button class="g-btn g-group-item" onclick='submit'>Search</button>
    </form>
      <?php view::widget_area('sidebar'); ?>
    </div>
</div>
