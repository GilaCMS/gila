<?php
$chartData = include __DIR__.'/chartData.php';
?>

<script>
var randomScalingFactor = function() {
  return Math.round(Math.random() * 100);
};

window.addEventListener("load",function(event) {
  window.<?=$chart?> = new Chart(document.getElementById('<?=$canvas_id?>'), {
	  type: 'radar',
	  data: <?=json_encode($chartData)?>,
	  options: {
	    legend: {
	      <?=($widget_data->legend==''?'display:false':'position:"'.$widget_data->legend.'"')?>
	    },
	    title: {
	      <?=($widget_data->title==''?'display:false':'display: true,text:"'.$widget_data->title.'"')?>
	    },
	    tooltips: {
	      display: <?=(isset($widget_data->tootips)?$widget_data->tootips:'false')?>
	    },
	    scale: {
	      ticks: {
	        beginAtZero: true
	      }
	    }
	  }
	});
});

</script>
