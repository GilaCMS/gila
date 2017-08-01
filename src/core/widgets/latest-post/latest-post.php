<div class="widget_title"><h2>Latest Posts</h2></div>
<ul class="g-nav g-navbar vertical">
<?php
global $db;
$res = $db->query("SELECT id,title FROM post ORDER BY id DESC LIMIT ?",$widget_data->n_post);
while ($r = mysqli_fetch_array($res)) {
	echo "<li><a href='{$r['id']}'>{$r['title']}</a></li>";
}
?>
</ul>
