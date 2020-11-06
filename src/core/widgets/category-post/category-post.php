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

$widget_data->n_post = @$widget_data->n_post?:4;
$widget_data->category = @$widget_data->category?:null;

foreach (Gila\Post::getPosts(
  ['posts'=>$widget_data->n_post, 'category'=>$widget_data->category]
) as $key=>$r) {
  $href = Config::make_url('blog', '', ['p'=>$r['id'],'slug'=>$r['slug']]);
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

?>
</ul>
</section>