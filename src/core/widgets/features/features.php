<section class="features-grid colors-<?=Config::option('colors')?>">
<?=View::css('core/widgets.css')?>
<?php foreach (json_decode(@$widget_data->features) as $feature) { ?>
    <div style="text-align:<?=htmlentities($data['align']??'center')?>">
        <?=View::img($feature[0], 300)?>
        <h3><?=htmlentities($feature[1])?></h3>
        <p><?=htmlentities($feature[2])?></p>
        <?php if ($feature[3] != '') {
  echo '<a href="'.htmlentities($feature[3]).'">Learn More</a>';
} ?>
    </div>
<?php } ?>
</section>
