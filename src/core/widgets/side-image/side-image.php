<?php
  $latte = new Latte\Engine;
  $latte->setTempDirectory(__DIR__."/../../latteTemp");
  $params = [
    'data' => $widget_data,
    'img' => View::thumb($widget_data->image, 600),
  ];
  // render to output
  $latte->render(__DIR__.'/widget.latte', $params);
?>
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

