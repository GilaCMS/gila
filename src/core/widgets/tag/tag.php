<div class="pad">
<?php
$widget_data->n = @$widget_data->n?:12;

global $db;
$res=$db->query("SELECT value,COUNT(*) AS n FROM postmeta
WHERE vartype='tag' AND (SELECT publish from post WHERE post.id=post_id)=1
GROUP BY value ORDER BY n DESC LIMIT 0,{$widget_data->n};");

while($r=mysqli_fetch_array($res)) {
  echo "<a class='g-btn btn-white' href='blog/tag/{$r[0]}'>{$r[0]}({$r[1]}) </a>";
}

echo '</div>';
