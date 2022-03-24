<section>
<div class="d-flex flex-wrap justify-content-center">
<?=View::script('lib/bootstrap5/bootstrap.bundle.min.js')?>
<?=View::cssAsync('lib/bootstrap5/bootstrap.min.css')?>
<?php foreach (json_decode(@$widget_data->features) as $feature) { ?>
    <div class="m-3" style="text-align:<?=htmlentities($data['align']??'center')?>">
        <?=View::img($feature[0], 300)?>
        <h3><?=htmlentities($feature[1])?></h3>
        <p><?=htmlentities($feature[2])?></p>
        <?php if ($feature[3] != '') {
  echo '<a href="'.htmlentities($feature[3]).'">Learn More</a>';
} ?>
    </div>
<?php } ?>
</div>
</section>
