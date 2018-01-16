<ul class="g-nav vertical">
<?php
if(!@class_exists('blog')) {
	include_once "src/core/controllers/blog.php";
	new blog();
}

foreach (blog::posts(['posts'=>$widget_data->n_post]) as $r ) {
	echo "<li><a href='".blog::get_url($r['id'],$r['slug'])."'>{$r['title']}</a></li>";
}
?>
</ul>
