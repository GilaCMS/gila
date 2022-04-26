<?=View::script('lib/bootstrap5/bootstrap.bundle.min.js')?>
<?=View::cssAsync('lib/bootstrap5/bootstrap.min.css')?>
<?php
  if(strlen($widget_data->images)>2 and round(count(json_decode($widget_data->images))/3)>0){
    $imgs = array_chunk(json_decode($widget_data->images), round(count(json_decode($widget_data->images))/$widget_data->columns??3));
  }else{
    echo "Debe agregar mas fotos";
  }
  $images0 = [];
  $images1 = [];
  $images2 = [];
  foreach ($imgs[0] as $img) {
    array_push($images0,htmlentities(View::thumb($img[0], 300)));
  }
  foreach ($imgs[1] as $img) {
    array_push($images1,htmlentities(View::thumb($img[0], 300)));
  }
  foreach ($imgs[2] as $img) {
    array_push($images2,htmlentities(View::thumb($img[0], 300)));
  }
  $latte = new Latte\Engine;
  $latte->setTempDirectory(__DIR__."/../../latteTemp");
  $params = [
    'imgs0' => $images0,
    'imgs1' => $images1,
    'imgs2' => $images2,
  ];
  // render to output
  $latte->render(__DIR__.'/widget.latte', $params);
?>
<!-- <section>
  <div class="container" style="text-align:center">
    <div class="row">
      <div class="col-lg-4 col-md-12 mb-4 mb-lg-0">
        <?php if (isset($widget_data->images)) {
          foreach ($imgs[0] as $img) { ?>
            <img src="<?=htmlentities(View::thumb($img[0], 300))?>" class="w-100 shadow-1-strong rounded mb-4">
            <?php }
        } ?>
      </div>
      <div class="col-lg-4 col-md-12 mb-4 mb-lg-0">
        <?php if (isset($widget_data->images)) {
          foreach ($imgs[1] as $img) { ?>
            <img src="<?=htmlentities(View::thumb($img[0], 300))?>" class="w-100 shadow-1-strong rounded mb-4">
            <?php }
        } ?>
      </div>
      <div class="col-lg-4 col-md-12 mb-4 mb-lg-0">
        <?php if (isset($widget_data->images)) {
          foreach ($imgs[2] as $img) { ?>
            <img src="<?=htmlentities(View::thumb($img[0], 300))?>" class="w-100 shadow-1-strong rounded mb-4">
            <?php }
        } ?>
      </div>
    </div>
  </div>
</section>
 -->
 