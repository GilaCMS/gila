<?=View::script('assets/mysql-chart/chartjs/Chart.bundle.min.js')?>
<?php
global $db;
$wid = $widget_data->widget_id;
$canvas_id = 'wdgt'.$wid.'cnvs';
$ctx = 'wdgt'.$wid.'ctx';
$chart = 'wdgt'.$wid.'chart';
$flex = explode(' ', $widget_data->flex) ?? ['auto','auto'];
?>
<style>.widget-side-chart{grid-column: span 3;}</style>
<section class="container" style="align-items: center;display:grid; padding:1em;
grid-template-columns: repeat(auto-fit, minmax(360px,1fr)); grid-gap: 2em;">
<?php if ($widget_data->side) { ?>
  <div><?=$widget_data->text?></div>
  <div style="width:100%;"><canvas id="<?=$canvas_id?>"></canvas></div>
<?php } else { ?>
  <div style="width:100%;"><canvas id="<?=$canvas_id?>"></canvas></div>
  <div data-inline="text"><?=$widget_data->text?></div>
<?php } ?>
</section>
<?php
$queries = Config::getList('chartjs-query');
if (isset($queries[$widget_data->data])) {
  $widget_data->query = $queries[$widget_data->data]['query'];
  $widget_data->data = 'mysql';
}

$db->query('SET SESSION TRANSACTION READ ONLY;');
include __DIR__.'/../common/chart.'.$widget_data->type.'.php';