<?=View::script('lib/bootstrap5/bootstrap.bundle.min.js')?>
<?=View::cssAsync('lib/bootstrap5/bootstrap.min.css')?>
<style>
  .side-image{align-items: center;display:grid; padding:1em;
  grid-template-columns: repeat(auto-fit, minmax(360px,1fr)); grid-gap: 2em;}
  @media (min-width:801px){
    .side-image{margin:auto}
    .side-image>.col1{grid-row:1;grid-column:1;}
    .side-image>.col2{grid-row:1;grid-column:2;<?=($widget_data->side==0?'2':'1')?>}
  }
  @media (max-width:400px){
    .side-image{grid-template-columns: 1fr}
  }
</style>
<section>
  <div class="container side-image">
    <img src="<?=View::thumb($widget_data->image, 600)?>" style="max-height:600px;margin:auto;" class="<?=($widget_data->side==0?'col1':'col2')?>">
    <div data-inline="text" class="<?=($widget_data->side==0?'col2':'col1')?>"><?=$widget_data->text?></div>
  </div>
</section>
