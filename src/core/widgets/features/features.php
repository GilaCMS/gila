<style>[fill="#6c63ff"]{fill:orangered}</style>
<style>.widget-features{grid-column: 1/span 4;text-align: center;}.features-grid div{text-align:center;padding:1em}.features-grid img,.features-grid svg{max-width:100%;height:auto;}</style>
<div class="features-grid" style="display: grid; grid-gap:20px;padding:20px;justify-content: center; grid-template-columns: repeat(auto-fit, minmax(160px,200px)); width:100%">
<?php foreach (json_decode(@$widget_data->features) as $feature) { ?>
    <div>
        <?=View::img($feature[0], 300)?>
        <h3><?=htmlentities($feature[1])?></h3>
        <p><?=htmlentities($feature[2])?></p>
        <?php if ($feature[3] != '') {
  echo '<a href="'._url($feature[3]).'">Learn More</a>';
} ?>
    </div>
<?php } ?>
</div>
