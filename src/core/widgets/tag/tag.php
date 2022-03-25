<?=View::css('core/gila.min.css')?>
<?=View::script('lib/bootstrap5/bootstrap.bundle.min.js')?>
<?=View::cssAsync('lib/bootstrap5/bootstrap.min.css')?>
<div class="pad widget-tags">
<?php
$data['n'] ??= 12;

global $db;
$res=$db->query("SELECT value,COUNT(*) AS n FROM postmeta
WHERE vartype='tag' AND (SELECT publish from post WHERE post.id=post_id)=1
GROUP BY value ORDER BY n DESC LIMIT 0,{$data['n']};");

while ($r=mysqli_fetch_array($res)) {
  $tag = trim($r[0]);
  echo "<a class='btn btn-light' href='blog/tag/{$tag}'>{$tag}({$r[1]}) </a>";
}

echo '</div>';
