<!-- Post Header -->
<!-- Set your background image for this header on the line below. -->
<div class="wrapper">
  <h1><?=$title?></h1>
      <span class="meta">Posted by <a href="#"><?=$author?></a> on <?=date('F j, Y',strtotime($updated))?></span>
  <hr>

  <!-- Post Content -->
  <article>
      <?=nl2br($text)?>
  </article>
</div>
