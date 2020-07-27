<?=View::css('core/widgets.css')?>
<div class="features-grid">
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
