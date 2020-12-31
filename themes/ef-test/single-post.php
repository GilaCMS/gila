<div class="container">
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
      <form method="get" class="inline-flex " action="<?=Config::base('blog')?>">
        <input style="border-radius:10px;" name='search' class="g-input shadow-4 fullwidth" value="<?=(isset($search)? htmlentities($search):'')?>">
        <button  class="ml-2 bg-danger rounded-circle p-3 shadow-4 text-white" uk-icon="icon: search" onclick='submit'></button>
     </form>
  <?php View::widgetArea('sidebar'); ?>
</div>
</div>

</div>