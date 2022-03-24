<?php
global $chartColors;
$chartData = include __DIR__.'/chartData.php';
foreach ($chartData['datasets'] as $ids=>$ds) {
  $chartData['datasets'][$ids]['backgroundColor'] = [];
  $chartData['datasets'][$ids]['borderWidth'] = 0;
  foreach ($ds['data'] as $iv => $value) {
    $chartData['datasets'][$ids]['backgroundColor'][$iv] = $chartColors[$iv];
  }
}
?>

<script>
var randomScalingFactor = function() {
  return Math.round(Math.random() * 100);
};

window.addEventListener("load",function(event) {
  var <?=$ctx?> = document.getElementById('<?=$canvas_id?>').getContext('2d');
  var <?=$chart?> = new Chart(<?=$ctx?>, {
	  type: 'pie',
	  data: <?=json_encode($chartData)?>,
	  options: {
	    legend: {
	      <?=($widget_data->legend==''?'display:false':'position:"'.$widget_data->legend.'"')?>
	    },
	    title: {
	      <?=($widget_data->title==''?'display:false':'display: true,text:"'.$widget_data->title.'"')?>
	    },
	    responsive: true
	  }
	});
});

</script>
