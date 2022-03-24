<?php
$chartData = include __DIR__.'/chartData.php';
?>

<script>
var randomScalingFactor = function() {
  return Math.round(Math.random() * 100);
};

window.addEventListener("load",function(event) {
    var <?=$ctx?> = document.getElementById('<?=$canvas_id?>').getContext('2d');
    var <?=$chart?> = new Chart(<?=$ctx?>, {
        type: 'bar',
        data: <?=json_encode($chartData)?>,
        options: {
            responsive: true,
            scaleOverride: true,
            legend: {
                <?=($widget_data->legend==''?'display:false':'position:"'.$widget_data->legend.'"')?>
            },
            title: {
                display: true,
                text: '<?=$widget_data->title?>'
            },
			scales: {
				yAxes: [{
					ticks: {
                        beginAtZero: true
                    }
				}]
			}
        }
    });
});

</script>
