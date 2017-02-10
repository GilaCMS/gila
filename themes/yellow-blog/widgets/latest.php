
    <div class="clearfix single_sidebar">
        <div class="popular_post">
            <div class="sidebar_title"><h2>Recent Post</h2></div>
            <ul>
<?php
global $db;
$res = $db->query("SELECT id,title FROM post ORDER BY id DESC LIMIT 5");
if ($res) while ($r = mysqli_fetch_array($res)) {
	echo "<li><a href='{$r['id']}'>{$r['title']}</a></li>";
}
?>
            </ul>
        </div>
</div>
