
<!-- Posts -->
<div class="row">
    <div class="gm-9 wrapper" style="border-right: 1px dashed #ccc">
    <?php view::widget_area('frontpage'); ?>
    <?php foreach ($c->posts as $r) { ?>
    <div class="gm-12 row gap-8px post-review">
            <?php
            if($img=view::thumb_sm($r['img'],$r['id'].'__sm.jpg')){
		       $title_gl='gs-9';
		       echo '<div class="gs-3">';
               echo '<img src="'.$img.'" style="width:100%; height:auto">';
		       echo '</div>';
            } else $title_gl='gm-12';
            ?>

        <div class="<?=$title_gl?>">
            <a href="<?=blog::get_url($r['id'],$r['slug'])?>">
                <h2 class="post-title" style="margin-top:0"><?=$r['title']?></h2>
            </a>
            <?=$r['description']?>
        </div>
    </div>
    <?php } ?>

    <?php view::renderFile('pagination.php')?>
    </div>
    <div class="gm-3 sidebar">
      <form method="get" class="inline-flex" action="<?=gila::base_url('blog')?>">
        <input name='search' class="g-input fullwidth" value="<?=($search??'')?>">
        <button class="g-btn g-group-item" onclick='submit'>Search</button>
    </form>
      <?php view::widget_area('sidebar'); ?>
    </div>
</div>
