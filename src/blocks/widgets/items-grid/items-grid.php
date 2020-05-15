<style>.widget-items{grid-column: 1/span 4;text-align: center;}
.items-grid>div{text-align:left;padding:1em;display:grid;grid-template-columns:1fr 2fr;grid-gap: 0.5em;}
.items-grid img,.items-grid svg{max-width:100%;height:auto;}</style>
<div class="items-grid" style="display: grid; grid-gap:20px;padding:20px;justify-content: center; grid-template-columns: repeat(auto-fit, minmax(240px,320px)); width:100%">
<?php foreach(json_decode(@$widget_data->items) as $item) { ?>
  <div>
    <div>
      <?=View::img($item[0], 300)?>
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
