
<!-- Posts -->
<div class="container">
<div class="row wrapper">
    <div class="gm-9">
    <?php foreach ($posts as $r) { ?>        
    <div  class="row gap-8px post-review ">
            <?php
            if($img=View::thumb($r['img'], 200)){
		       $title_gl='gs-9';
		       echo '<div class="gs-3">';
               echo '<img class="lazy" data-src="'.$img.'" style="width:100%; height:auto">';
		       echo '</div>';
            } else $title_gl='gm-12';
            ?>

        <div class="<?=$title_gl?>">
        <div  class="uk-card text-center uk-card-default uk-card-body uk-width-1-2@m ">
            <h3 class="uk-card-title"><?=$r['title']?></h3>
            <p><?=strip_tags($r['post'])?></p>
            <a class="uk-button uk-button-danger" href="<?=$r['url']?>"> Devamını oku  </a>
        </div>
        </div>
    </div><!--hr-->
    <?php } ?>
    <!-- Pagination -->
    <?php View::renderFile('pagination.php')?>
    </div>
    <div class=" sidebar">
      <form method="get" class="inline-flex " action="<?=Config::base('blog')?>">
        <input style="border-radius:10px;" name='search' class="g-input shadow-4 fullwidth" value="<?=(isset($search)? htmlentities($search):'')?>">
        <button  class="ml-2 bg-danger rounded-circle p-3 shadow-4 text-white" uk-icon="icon: search" onclick='submit'></button>
     </form>
      <?php View::widgetArea('sidebar'); ?>
    </div>
</div>
</div>
