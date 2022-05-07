<?=View::script('lib/bootstrap5/bootstrap.bundle.min.js')?>
<?=View::cssAsync('lib/bootstrap5/bootstrap.min.css')?>
<?php 
$chunks = array_chunk(json_decode($widget_data->images??'[]'), $widget_data->columns??3);
?>
<section>
  <div class="container" style="text-align:center">
    <div class="row">
    <?php foreach ($chunks as $chunk) : ?>
      <div class="col-lg-4 col-md-12 mb-4 mb-lg-0">
      <?php
      foreach ($chunk as $img) : ?>
        <img src="<?=htmlentities(View::thumb($img[0], 300))?>" class="w-100 shadow-1-strong rounded mb-4">
      <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
    </div>
  </div>
</section>
