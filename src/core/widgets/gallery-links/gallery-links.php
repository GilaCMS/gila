<?=View::script('lib/bootstrap5/bootstrap.bundle.min.js')?>
<?=View::cssAsync('lib/bootstrap5/bootstrap.min.css')?>
<?php 
$imgs = array_chunk(json_decode($widget_data->images), round(count(json_decode($widget_data->images))/3));
?>
<div class="row">
  <div class="col-lg-4 col-md-12 mb-4 mb-lg-0">
  <?php if (isset($widget_data->images)) {
    foreach ($imgs[0] as $img) {
      if ($img[2]) {
        echo '<a href="'.htmlentities($img[2]).'">';
      } ?>
      <img src="<?=htmlentities(View::thumb($img[0], 400))?>" alt="<?=htmlentities($img[1])?>" class="w-100 shadow-1-strong rounded mb-4">
      <?php if ($img[2]) {
        echo '</a>';
      }
    }
  } ?>
  </div>
  <div class="col-lg-4 col-md-12 mb-4 mb-lg-0">
  <?php if (isset($widget_data->images)) {
    foreach ($imgs[1] as $img) {
      if ($img[2]) {
        echo '<a href="'.htmlentities($img[2]).'">';
      } ?>
      <img src="<?=htmlentities(View::thumb($img[0], 400))?>" alt="<?=htmlentities($img[1])?>" class="w-100 shadow-1-strong rounded mb-4">
      <?php if ($img[2]) {
        echo '</a>';
      }
    }
  } ?>
  </div>
  <div class="col-lg-4 col-md-12 mb-4 mb-lg-0">
  <?php if (isset($widget_data->images)) {
    foreach ($imgs[2] as $img) {
      if ($img[2]) {
        echo '<a href="'.htmlentities($img[2]).'">';
      } ?>
      <img src="<?=htmlentities(View::thumb($img[0], 400))?>" alt="<?=htmlentities($img[1])?>" class="w-100 shadow-1-strong rounded mb-4">
      <?php if ($img[2]) {
        echo '</a>';
      }
    }
  } ?>
  </div>
</div>

