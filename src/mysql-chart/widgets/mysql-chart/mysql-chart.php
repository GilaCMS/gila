<?=View::script('assets/mysql-chart/chartjs/Chart.bundle.min.js')?>
<?php
global $db;
$wid = $data['widget_id'];
$canvas_id = 'wdgt'.$wid.'cnvs';
$ctx = 'wdgt'.$wid.'ctx';
$chart = 'wdgt'.$wid.'chart';
?>
<style>.widget-mysql-chart{grid-column: span 2;}</style>
<div style="width:100%;" class="container">
  <canvas id="<?=$canvas_id?>"></canvas>
</div>
<?php
$queries = Config::getList('chartjs-query');
if (isset($queries[$data['data']])) {
  $data['query'] = $queries[$data['data']]['query'];
  $data['data'] = 'mysql';
}

include __DIR__.'/../common/chart.'.$data['type'].'.php';
