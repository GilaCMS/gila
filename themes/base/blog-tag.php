
<!-- Posts -->
<div class="wrapper row gap-8px" style="background:#e8e8e8;display: flex; justify-content: center;">
    <div class="gs-12" style="max-width:900px">
    <?php foreach ($posts_by_tag as $r) { ?>
    <div class="gs-12">
    <div class="bordered  row" style="background:white">
            <?php
            if($img=view::thumb_sm($r['img'],$r['id'].'__sm.jpg')){
		$title_gl='gs-9';
		echo '<div class="gs-3 wrapper">';
                echo '<img src="'.$img.'" style="width:100%; height:auto">';
		echo '</div>';
            } else $title_gl='gs-12';
            ?>

        <div class="<?=$title_gl?> wrapper">
            <a href="<?=$r['id']?>">
                <h3 class="post-title" style="margin-top:0"><?=$r['title']?></h3>
            </a>
            <?=nl2br(strip_tags($r['post']))?>
        </div>
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
</div>
