<section style="background:url('<?=htmlentities($widget_data->image)?>');
background-size:cover;height:<?=htmlentities($widget_data->height)?>;
position:relative">
  <div data-inline="text" style="height: 50%; position: absolute; left: 20%; transform: TranslateX(-10%) TranslateY(50%);
  font-size:<?=htmlentities($widget_data->text_size??'100%')?>;text-shadow:0 0 4px rgba(0,0,0,0.4);
  text-align:<?=htmlentities($widget_data->align??'center')?>;color:#fff">
    <?=$widget_data->text?>
  </div>
</section>