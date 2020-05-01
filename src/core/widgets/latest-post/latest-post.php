<ul class="g-nav vertical">
<?php

if(!@class_exists('blog')) if(file_exists("src/blog/controllers/blog.php")){
  include_once "src/blog/controllers/blog.php";
  new blog();
} else return;

if($widget_data->show_thumbnails == 1) {
  $stacked_file = SITE_PATH.'tmp/stacked-wdgt'.$widget_data->widget_id.'.jpg';
  $posts = [];
  $img = [];
  $widget_data->n_post = @$widget_data->n_post?:5;
  $widget_data->show_thumbnails = @$widget_data->show_thumbnails?:0;
  foreach (core\models\post::getLatest($widget_data->n_post) as $r ) {
    $posts[] = $r;
    $img[]=$r['img'];
  }
  list($stacked_file, $stacked) = View::thumb_stack($img, $stacked_file,80,80);
} else {
  foreach (core\models\post::getLatest($widget_data->n_post) as $r ) {
    $posts[] = $r;
  }
}


foreach ($posts as $key=>$r ) {
  echo "<li>";
  echo "<a href='".blog::get_url($r['id'],$r['slug'])."'>";
  if($widget_data->show_thumbnails == 1) if($stacked[$key]!==false){
    if($img=View::thumb_xs($r['img'])) {
      echo "<img src='$img' style='float:left;margin-right:6px'> ";
    } else echo "<div style='width:80px;height:40px;float:left;margin-right:6px'></div> ";
  } else {
    $i = $stacked[$key];
    echo "<div style='background:url(\"$stacked_file\") 0 -{$i['top']}px; width:{$i['width']}px;height:{$i['height']}px;float:left;margin-right:6px'></div> ";
  }
  echo "{$r['title']}</a></li>";
}
?>
</ul>
