<div class="row wrapper">
<div class="gm-9">
  <h1><?=$title?></h1>
      <span class="meta">Posted by <a href="<?=Config::url('blog/author/'.$author_id)?>"><?=htmlentities($author)?></a> on <?=date('F j, Y',strtotime($updated))?></span>
  <hr>

  <!-- Post Content nl2br($text) -->
  <article>
      <?=$text?>
  </article>
  <?php View::widgetArea('post.after'); ?>
</div>

<div class="gm-3 sidebar">
  <form method="get" class="inline-flex" action="<?=Config::base_url('blog')?>">
    <input name='search' class="g-input fullwidth" value="<?=(isset($_GET['search'])?$_GET['search']:'')?>">
    <button class="g-btn g-group-item" onclick='submit'><?=__('Search')?></button>
  </form>
  <?php View::widgetArea('sidebar'); ?>
</div>
</div>
