<div class="row" style="">
<div class="gm-9">
  <h1><?=$title?></h1>
      <span class="meta">Posted by <a href="#"><?=$author?></a> on <?=date('F j, Y',strtotime($updated))?></span>
  <hr>

  <!-- Post Content nl2br($text) -->
  <article>
      <?=$text?>
  </article>
  <?php view::widget_area('post.after'); ?>
</div>

<div class="gm-3 sidebar">
  <form method="get" class="inline-flex" action="<?=gila::make_url('blog')?>">
    <input name='search' class="g-input fullwidth" value="<?=(isset($_GET['search'])?$_GET['search']:'')?>">
    <button class="g-btn g-group-item" onclick='submit'>Search</button>
  </form>
  <?php view::widget_area('sidebar'); ?>
</div>
</div>
