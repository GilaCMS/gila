<ul class="g-nav vertical">
<?php

if (!@class_exists('blog')) {
  if (file_exists("src/blog/controllers/BlogController.php")) {
    include_once "src/blog/controllers/BlogController.php";
    new BlogController();
  } else {
    return;
  }
}

if ($widget_data->show_thumbnails == 1) {
  $stacked_file = TMP_PATH.'/stacked-wdgt'.$widget_data->widget_id.'.jpg';
  $posts = [];
  $img = [];
  $widget_data->n_post = $widget_data->n_post ?? 5;
  $widget_data->show_thumbnails = $widget_data->show_thumbnails ?? 0;
  foreach (Gila\Post::getLatest($widget_data->n_post) as $r) {
    $posts[] = $r;
    $img[]=$r['img'];
  }
  list($stacked_file, $stacked) = View::thumbStack($img, $stacked_file, 80, 80);
} else {
  foreach (Gila\Post::getLatest($widget_data->n_post) as $r) {
    $posts[] = $r;
  }
}


foreach ($posts as $key=>$r) {
  echo "<li>";
  echo "<a class='text-decoration-none' href='".Config::base('blog/'.$r['id'].'/'.$r['slug'])."'>";
  if ($widget_data->show_thumbnails == 1) {
    if ($stacked[$key]!==false) {
      if ($img=View::thumb($r['img'], 100)) {
        echo "<img src='$img' style='float:left;margin-right:6px'> ";
      } else {
        echo "<div style='width:80px;height:40px;float:left;margin-right:6px'></div> ";
      }
    } else {
      $i = $stacked[$key];
      echo "<div style='background:url(\"$stacked_file\") 0 -{$i['top']}px; width:{$i['width']}px;height:{$i['height']}px;float:left;margin-right:6px'></div> ";
    }
  }
  echo "{$r['title']}</a></li>";
}
?>
</ul>
