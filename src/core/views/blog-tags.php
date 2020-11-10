<?php
usort($tags, function ($a, $b) {
  return $a['value'] - $b['value'];
});

/* save for PHP 7+
usort($tags, function($a, $b) {
    return $a['value'] <=> $b['value'];
});
*/
$letter = '';
foreach ($tags as $tag) {
  $name = $tag['value'];
  $url = Config::url('blog/tag', ['tag'=>$name]);
  if ($letter != ucfirst($name[0])) {
    $letter = ucfirst($name[0]);
    echo '<h2>'.$letter.'</h2><hr>';
  }
  echo '<a href="'.$url.'">'.$name.' '.$tag['count'].'</a><br>';
}
