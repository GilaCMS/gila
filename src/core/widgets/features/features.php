<div class="features-grid" style="display: grid; grid-gap:20px;justify-content: center; grid-template-columns: repeat(auto-fit, minmax(160px,200px)); width:100%">
<?php foreach(json_decode(@$widget_data->features) as $feature) { ?>
    <div>
        <img height="410" src="<?=view::thumb($feature[0],200)?>" alt=""></a>
        <h3><?=$feature[1]?></h3>
        <p><?=$feature[2]?></p>
        <?php if($feature[3] != '') {
            echo '<a href="'.$feature[3].'">Learn More</a>';
        } ?>
    </div>
<?php } ?>
</div>
