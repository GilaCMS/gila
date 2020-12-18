
<!-- Posts -->
<div class="row wrapper">
    <div class="gm-9">
    <?php foreach ($posts as $r) { ?>
    <div class="gm-12 row gap-8px post-review">
            <?php
            if ($img=View::thumb($r['img'], 200)) {
              $title_gl='gs-9';
              echo '<div class="gs-3">';
              echo '<img class="lazy" data-src="'.$img.'" style="width:100%; height:auto">';
              echo '</div>';
            } else {
              $title_gl='gm-12';
            }
            ?>

        <div class="<?=$title_gl?>">
            <a href="<?=$r['url']?>">
                <h2 class="post-title" style="margin-top:0"><?=$r['title']?></h2>
            </a>
            <?=strip_tags($r['post'])?>
        </div>
    </div><!--hr-->
    <?php } ?>
    <!-- Pagination -->
    <?php View::renderFile('pagination.php')?>
    </div>
    <div class="gm-3 sidebar">
      <form method="get" class="inline-flex" action="<?=Config::base('blog')?>">
        <input name='search' class="g-input fullwidth" value="<?=($search??'')?>">
        <button class="g-btn g-group-item" onclick='submit'><?=__("Search")?></button>
    </form>
      <?php View::widgetArea('sidebar'); ?>
    </div>
</div>
