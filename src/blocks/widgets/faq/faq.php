<section class="wrapper faq">
<?php foreach (json_decode(@$widget_data->items) as $item) { ?>
  <h3><?=$item[0]?></h3>
  <p><?=$item[1]?></p>
<?php } ?>
</section>
