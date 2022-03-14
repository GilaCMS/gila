<div class="row" style="">
<div class="gm-9">
  <!-- Post Content nl2br($text) -->
  <article>
    <h1><?=$title?></h1>
        <span class="meta">Posted by <a href="<?=Config::url('blog/author/'.$author_id)?>"><?=htmlentities($author)?></a> on <?=date('F j, Y', strtotime($updated))?></span>
    <hr>
    <?=$text?>
  </article>
  <?php View::widgetArea('post.after'); ?>
</div>

<div class="gm-3 sidebar">
  <form method="get" class="inline-flex" action="<?=Config::base('blog')?>">
    <input name='search' class="g-input fullwidth" value="">
    <button class="g-btn g-group-item" onclick='submit'><?=__('Search')?></button>
  </form>
  <?php View::widgetArea('sidebar'); ?>
</div>
</div>
