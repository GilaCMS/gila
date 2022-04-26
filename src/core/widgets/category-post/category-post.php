<section>
<?=View::css('core/widgets.css')?>
<ul class="g-nav vertical five-post">
<?php
if (!@class_exists('blog')) {
  if (file_exists("src/blog/controllers/BlogController.php")) {
    include_once "src/blog/controllers/BlogController.php";
    new BlogController();
  } else {
    return;
  }
}

$widget_data->n_post = $widget_data->n_post??4;
$widget_data->category = $widget_data->category??null;

foreach (Gila\Post::getPosts(
  ['posts'=>$widget_data->n_post, 'category'=>$widget_data->category]
) as $key=>$r) {
  $href = Config::url('blog/'.$r['id'].'/'.$r['slug']);
  echo "<li>";
  echo "<a href='$href'>";
  if ($key==0) {
    if ($img=View::thumb($r['img'], 600)) {
      echo "<img src='$img'>";
    }
  } elseif ($img=View::thumb($r['img'], 400)) {
    echo "<img src='$img'>";
  }
  echo "</a><div><a href='$href' class='post-title'>{$r['title']}</a>";
  if ($key==0) {
    echo "<br>".($r['description']??$r['post']);
  }
  echo "</div></li>";
}
$latte = new Latte\Engine;
  $latte->setTempDirectory(__DIR__."/../../latteTemp");
  $imgs = [];
  $posts = Gila\Post::getPosts(['posts'=>$widget_data->n_post, 'category'=>$widget_data->category]);
  foreach ($posts as $key=>$r){
    array_push($imgs, View::thumb($r['img'], 400));
  }
  $params = [
    'widget_data' => $widget_data,
    'imgs' => $imgs,
    'posts' => Gila\Post::getPosts(['posts'=>$widget_data->n_post, 'category'=>$widget_data->category])
  ];
  // render to output
  //$latte->render(__DIR__.'/widget.latte', $params);
?>
</ul>
</section>