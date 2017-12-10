
<ul class="g-nav vertical">
<?php
foreach (blog::posts(['posts'=>$widget_data->n_post]) as $r ) {
	echo "<li><a href='".blog::get_url($r['id'],$r['slug'])."'>{$r['title']}</a></li>";
}
?>
</ul>
