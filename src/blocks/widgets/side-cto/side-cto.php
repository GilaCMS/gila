<section class="side-cto" style="align-items: center;display:grid; padding:1em;
grid-template-columns: repeat(auto-fit, minmax(360px,1fr)); grid-gap: 2em;">
  <div>
    <h2 class="section-heading"><?=$widget_data->heading?></h2>
    <p class="lead-text"><?=$widget_data->text?></p>
    <a href="<?=$widget_data->primary_link?>" class="btn btn-primary btn-lg"><?=$widget_data->primary_text?></a>&nbsp;
    <a href="<?=$widget_data->secondary_link?>" class="btn btn-secondary btn-grey btn-lg"><?=$widget_data->secondary_text?></a>
  </div>
  <img src="<?=$widget_data->image?>" style="max-height:300px;margin:auto">
</section>
