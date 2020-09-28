<section>
<?=View::css('core/gila.min.css')?>
<div class="gallery">
  <?php if (isset($widget_data->images)) {
  foreach (json_decode($widget_data->images) as $img) { ?>
  <img src="<?=htmlentities(View::thumb($img[0]))?>">
  <?php }
} ?>
</div>
</section>
