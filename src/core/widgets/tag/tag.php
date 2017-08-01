<?php

global $db;
$res=$db->query("SELECT value,COUNT(*) AS n FROM postmeta WHERE vartype='tag' GROUP BY value ORDER BY n DESC LIMIT 0,{$widget_data->n};");

while($r=mysqli_fetch_array($res)) {
  echo "<a href='blog/tag/{$r[0]}' class='g-btn btn-white'>{$r[0]}({$r[1]}) </a>";
}
