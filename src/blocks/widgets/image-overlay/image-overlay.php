<section style="background:url('<?=htmlentities($widget_data->image)?>');
background-size:cover;height:<?=htmlentities($widget_data->height)?>;
position:relative">
  <div data-inline="text" style="height: 50%; position: absolute; left: 20%; transform: TranslateX(-10%) TranslateY(50%);
  color:<?=htmlentities($widget_data->text_color??'inherit')?>;
  text-align:<?=htmlentities($widget_data->align??'center')?>">
    <?=$widget_data->text?>
  </div>
</section>