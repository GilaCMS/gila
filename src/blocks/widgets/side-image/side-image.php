<?php
$flex = explode(' ', $widget_data->flex) ?? ['auto','auto'];
?>
<section style="align-items: center;display:grid; padding:1em;
grid-template-columns: repeat(auto-fit, minmax(360px,1fr)); grid-gap: 2em;">
<?php if ($widget_data->side) { ?>
  <div><?=$widget_data->text?></div>
  <img src="<?=$widget_data->image?>" style="max-height:300px;margin:auto">
<?php } else { ?>
  <img src="<?=$widget_data->image?>" style="max-height:300px;margin:auto">
  <div><?=$widget_data->text?></div>
<?php } ?>
</section>