<div class="clearfix slider">
    <ul class="pgwSlider">
<?php
global $db;
$res = $db->query("SELECT id,title,SUBSTRING(post,1,120) as post,(SELECT value FROM postmeta WHERE post_id=post.id AND vartype='thumbnail') as img FROM post ORDER BY id DESC LIMIT 3");
while ($r = mysqli_fetch_array($res)) {
echo "<li><a href=\"{$r['id']}\"><img src=\"{$r['img']}\" alt=\"{$r['title']}\" data-large-src=\"{$r['img']}\" data-description=\"{$r['post']}\"/></a></li>";
}
?>
    </ul>
</div>
