<style>.widget-items{grid-column: 1/span 4;text-align: center;}
.items-list>div{text-align:left;padding:1em;display:grid;grid-template-columns:1fr 2fr;}
.items-list img,.items-list svg{max-width:100%;height:auto;}</style>
<div style="display: grid; grid-gap:20px;padding:20px;grid-template-columns:1fr 1fr">
<div class="items-list" style="display: grid; grid-gap:20px;padding:20px;justify-content: center; grid-template-columns: repeat(auto-fit, minmax(150px,200px)); width:100%">
<?php foreach(json_decode(@$widget_data->items) as $item) { ?>
  <div>
    <div>
      <?=view::img($item[0], 300)?>
    </div>
    <div>
      <h3><?=htmlentities($item[1])?></h3>
      <p><?=htmlentities($item[2])?></p>
      <?php if($item[3] != '') {
        echo '<a href="'._url($item[3]).'">Learn More</a>';
      } ?>
    </div>
  </div>
<?php } ?>
</div>
  <div>
    <?=view::img($widget_data->image, 600)?>
  </div>
</div>
