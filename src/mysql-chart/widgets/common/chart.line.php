<?php
$chartData = include __DIR__.'/chartData.php';
?>

<script>

window.addEventListener("load",function(event) {
	var <?=$ctx?> = document.getElementById('<?=$canvas_id?>').getContext('2d');
	window.<?=$chart?> = new Chart(<?=$ctx?>, {
		type: 'line',
		data: <?=json_encode($chartData)?>,
		options: {
			responsive: true,
			title: {
				<?=($data['title']==''?'display:false':'display: true,text:"'.$data['title'].'"')?>
			},
			tooltips: {
				mode: 'index',
				intersect: false,
			},
			hover: {
				mode: 'nearest',
				//intersect: true
			},
			scales: {
				xAxes: [{
					display: true,
					/*scaleLabel: {
						display: true,
						labelString: 'Month'
					}*/
				}],
				yAxes: [{
					display: true,
					/*scaleLabel: {
						display: true,
						labelString: 'Value'
					}*/
				}]
			}
		}
	});
});

</script>
