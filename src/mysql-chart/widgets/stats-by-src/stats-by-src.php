<?=View::script('assets/mysql-chart/chartjs/Chart.bundle.min.js')?>
<?php
global $db;
$wid = $widget_data->widget_id;
$canvas_id = 'wdgt'.$wid.'cnvs';
$ctx = 'wdgt'.$wid.'ctx';
$chart = 'wdgt'.$wid.'chart';
$chartColors = [
    'rgb(255, 99, 132)',
    'rgb(75, 192, 192)',
    'rgb(255, 159, 64)',
    'rgb(255, 205, 86)',
    'rgb(54, 162, 235)',
    'rgb(153, 102, 255)',
    'rgb(201, 203, 207)'
];
$datasetsN = 0;
$dataOptions = ['web'=>'Page Views'];
foreach (Config::getList('chartjs-stat') as $item) {
  $dataOptions[$item['type']] = $item['label'];
}
$dataKey = $widget_data->data ?? 'web';
$dataGroup = $widget_data->groupby ?? null;
$dataLabel = $dataOptions[$dataKey];
?>
<style>.widget-stats-chart{grid-column: span 2;}</style>
<div style="width:100%;" class="container">
  <canvas id="<?=$canvas_id?>"></canvas>
</div>
<?php
$chartData = [];
$begin = date('Y-m-d', strtotime('-7 days'));
$end = date("Y-m-d");
$data = Logger::getStat($dataKey, [$begin, $end]);
if ($locale=Config::config('locale')) {
  setlocale(LC_ALL, $locale);
}
foreach ($data as $row) {
  $date = substr($row[0], 0, 10);
  $cIndex = $date.'--Views';
  $row[3] = json_decode($row[3]);
  if ($filters) {
    foreach ($filters as $key=>$value) {
      if ($value != $data[3][$key]) {
        continue;
      }
    }
  }
  if ($dataGroup) {
    $dataLabel = $row[3][$dataGroup] ?? 'None';
    $cIndex = $date.'--'.$dataLabel;
  }
  if (isset($count[$cIndex])) {
    $count[$cIndex][2]++;
    if (!$dataGroup) {
      if (!in_array($row[2], $dateIps[$date])) {
        $dateIps[$date][] = $row[2];
        $count[$date.'--Visits'][2]++;
      }
    }
  } else {
    $count[$cIndex] = [ucfirst(strftime("%a %e %b", strtotime($date))), Config::tr($dataLabel), 1];
    if (!$dataGroup) {
      $count[$date.'--Visits'] = [ucfirst(strftime("%a %e %b", strtotime($date))), Config::tr('Visitors', ['es'=>'Visitantes']), 1];
    }
    $dateIps[$date] = [$row[2]];
  }
}

$dataArray = array_values($count);
$data = [];

foreach ($dataArray as $r) {
  if (!in_array($r[0], $data['labels'])) {
    $data['labels'][]=$r[0];
  }
  if (!in_array($r[1], $datasets)) {
    $datasets[]=$r[1];
    $data['datasets'][] = [
      'label'=>$r[1],
      'borderWidth'=>2,
      'fill'=>false,
      'backgroundColor'=>$chartColors[$datasetsN],
      'borderColor'=>$chartColors[$datasetsN],
    ];
    $datasetsN++;
  }
  @$label_data[$r[0]][$r[1]] = $r[2];
}

foreach ($data['labels'] as $ilabel=>$label) {
  foreach ($data['datasets'] as $ids=>$ds) {
    if ($v = @$label_data[$label][$ds['label']]) {
      @$data['datasets'][$ids]['data'][$ilabel] = $v;
    } else {
      @$data['datasets'][$ids]['data'][$ilabel] = null;
    }
  }
}

$chartData = $data;
?>

<script>

window.addEventListener("load",function(event) {
	var <?=$ctx?> = document.getElementById('<?=$canvas_id?>').getContext('2d');
	window.<?=$chart?> = new Chart(<?=$ctx?>, {
		type: 'line',
		data: <?=json_encode($chartData)?>,
		options: {
			responsive: true,
      legend: {
        <?=($widget_data->legend==''?'display:false':'position:"'.$widget_data->legend.'"')?>
      },
			title: {
				<?=($widget_data->title==''?'display:false':'display: true,text:"'.$widget_data->title.'"')?>
			},
			tooltips: {
				mode: 'index',
				intersect: false,
			},
			hover: {
				mode: 'nearest'
			},
			scales: {
				xAxes: [{
					display: true
				}],
				yAxes: [{
          display: true,
          ticks: {
            beginAtZero: true
          }
				}]
			}
		}
	});
});

</script>
