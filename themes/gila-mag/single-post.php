<div class="row" style="">
<div class="gm-9">
  <h1><?=$title?></h1>
      <span class="meta">Posted by <a href="<?=gila::url('blog/author/'.$author_id)?>"><?=$author?></a> on <?=date('F j, Y',strtotime($updated))?></span>
  <hr>

  <article>
      <?=$text?>
  </article>
  <?php view::widget_area('post.after'); ?>
</div>

<div class="gm-3 sidebar">
  <form method="get" class="inline-flex" action="<?=gila::base_url('blog')?>">
    <input name='search' class="g-input fullwidth" value="">
    <button class="g-btn g-group-item" onclick='submit'><?=__('Search')?></button>
  </form>
  <?php view::widget_area('sidebar'); ?>
</div>
</div>
