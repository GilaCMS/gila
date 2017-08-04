<div class="widget_title pad"><b>Latest Posts</b></div>
<ul class="g-nav vertical">
<?php
global $db;
$res = $db->query("SELECT id,title FROM post ORDER BY id DESC LIMIT ?",$widget_data->n_post);
while ($r = mysqli_fetch_array($res)) {
	echo "<li><a href='{$r['id']}'>{$r['title']}</a></li>";
}
?>
</ul>
