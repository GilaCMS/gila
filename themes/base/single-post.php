<!--style>
pre{
    background-color: #f0f0f0;
    border-radius: 3px;
    padding: 6px;
    border: 1px solid #ccc;
}
</style-->

<!-- Post Header -->
<div style="background:#e8e8e8;display: flex; justify-content: center;">
<div class="wrapper gm-12" style="max-width:900px; background:#fff;">
  <h1><?=$title?></h1>
      <span class="meta">Posted by <a href="#"><?=$author?></a> on <?=date('F j, Y',strtotime($updated))?></span>
  <hr>

  <!-- Post Content nl2br($text) -->
  <article>
      <?=$text?>
  </article>
  <?php view::widget_area('post.after'); ?>
</div>
</div>
