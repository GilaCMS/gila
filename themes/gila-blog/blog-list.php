
<!-- Posts -->
<div class="row gap-8px" style="">
    <div class="gs-9 wrapper">
    <?php foreach ($posts as $r) { ?>
    <div class="gs-12 row gap-8px">
            <?php
            if($img=view::thumb_sm($r['img'],$r['id'].'__sm.jpg')){
		$title_gl='gs-9';
		echo '<div class="gs-3">';
                echo '<img src="'.$img.'" style="width:100%; height:auto">';
		echo '</div>';
            } else $title_gl='gs-12';
            ?>

        <div class="<?=$title_gl?>">
            <a href="<?=blog::get_url($r['id'],$r['slug'])?>">
                <h3 class="post-title" style="margin-top:0"><?=$r['title']?></h3>
            </a>
            <?=nl2br(strip_tags($r['post']))?>
        </div>
    </div><hr>
    <?php } ?>
    <!-- Pagination -->
    <ul class="g-nav">
        <li class="">
            <a href="?page=<?=$page+1?>">Older Posts &rarr;</a>
        </li>
    </ul>
    </div>
    <div class="gs-3">
      <form method="get" class="inline-flex" action="<?=gila::make_url('blog')?>">
        <input name='search' class="g-input fullwidth" value="<?=(isset($_GET['search'])?$_GET['search']:'')?>">
        <button class="g-btn" onclick='submit'>Search</button>
      </form>
      <?php view::widget_area('sidebar'); ?>
    </div>
</div>
