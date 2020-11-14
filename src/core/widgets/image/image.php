<section>
<figure class="container" style="margin:auto;max-width:90%;width: max-content;">
  <img src="<?=View::thumb($widget_data->image, 600)?>" alt="<?=($widget_data->alt_text??'')?>"
  style="vertical-align: middle;"/>
  <figcaption><?=$widget_data->caption?></figcaption>
</figure>
</section>
