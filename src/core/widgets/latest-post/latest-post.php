
<ul class="g-nav vertical">
<?php
foreach (blog::posts(['posts'=>$widget_data->n_post]) as $r ) {
	echo "<li><a href='{$r['id']}'>{$r['title']}</a></li>";
}
?>
</ul>
