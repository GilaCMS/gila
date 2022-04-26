<?=View::script('lib/bootstrap5/bootstrap.bundle.min.js')?>
<?=View::cssAsync('lib/bootstrap5/bootstrap.min.css')?>
<?php 
  if(strlen($widget_data->images)>2 and round(count(json_decode($widget_data->images))/3)>0){
    $imgs = array_chunk(json_decode($widget_data->images), round(count(json_decode($widget_data->images))/3));
  }else{
    echo "Debe agregar mas fotos";
  }
  $images0 = [];
  $link0 = [];
  $caption0 = [];
  $images1 = [];
  $link1 = [];
  $caption1 = [];
  $images2 = [];
  $link2 = [];
  $caption2 = [];
  foreach ($imgs[0] as $img) {
    array_push($images0,htmlentities(View::thumb($img[0], 300)));
    array_push($caption0,$img[2]);
    array_push($link0,$img[2]);
  }
  foreach ($imgs[1] as $img) {
    array_push($images1,htmlentities(View::thumb($img[0], 300)));
    array_push($caption1,$img[2]);
    array_push($link1,$img[2]);
  }
  foreach ($imgs[2] as $img) {
    array_push($images2,htmlentities(View::thumb($img[0], 300)));
    array_push($caption2,$img[2]);
    array_push($link2,$img[2]);
  }
  $latte = new Latte\Engine;
  $latte->setTempDirectory(__DIR__."/../../latteTemp");
  $params = [
    'imgs0' => $images0,
    'imgs1' => $images1,
    'imgs2' => $images2,
    'caption0' => $caption0,
    'caption1' => $caption1,
    'caption2' => $caption2,
    'link0' => $link0,
    'link1' => $link1,
    'link2' => $link2,
  ];
  // render to output
  $latte->render(__DIR__.'/widget.latte', $params);
?>
<!-- <div class="row">
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
-->
