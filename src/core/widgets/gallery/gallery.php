<div class="gallery">
  <?php if(isset($widget_data->images)) foreach(json_decode($widget_data->images) as $img) { ?>
  <img src="<?=_url(view::thumb($img[0]))?>">
  <?php } ?>
</div>
