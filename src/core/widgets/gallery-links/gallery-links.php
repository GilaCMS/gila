<div class="gallery">
  <?php if(isset($widget_data->images)) foreach(json_decode($widget_data->images) as $img) {
    if($img[2]) echo '<a href="'.htmlentities($img[2]).'">';
    ?>
  <img src="<?=_url(view::thumb_md($img[0]))?>" alt="<?=htmlentities($img[1])?>">
  <?php 
    if($img[2]) echo '</a>';
  } ?>
</div>
