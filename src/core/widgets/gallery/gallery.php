<section class="gallery-section container">
<?=View::css('core/widgets.css')?>
  <?php if (isset($widget_data->images)) {
  foreach (json_decode($widget_data->images) as $img) { ?>
  <img src="<?=htmlentities(View::thumb($img[0], 300))?>">
  <?php }
} ?>
</section>
