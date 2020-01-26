<?php
$flex = explode(' ',$widget_data->flex) ?? ['auto','auto'];
?>
<section style="align-items: center;display:grid; padding:1em;
grid-template-columns: repeat(auto-fit, minmax(240px,1fr)); grid-gap: 2em;">
<?php if($widget_data->side) { ?>
  <div style="flex:<?=$flex[1]?>"><?=$widget_data->text?></div>
  <img src="<?=$widget_data->image?>" style="flex:<?=$flex[0]?>;height:<?=$flex[0]?>">
<?php } else { ?>
  <img src="<?=$widget_data->image?>" style="flex:<?=$flex[0]?>;height:<?=$flex[0]?>">
  <div style="flex:<?=$flex[1]?>"><?=$widget_data->text?></div>
<?php } ?>
</section>