<?php
  echo '<style>';
  include_once(__DIR__.'/style.css');
  echo '</style>';
?>
<ul class="g-nav vertical five-post">
<?php
if(!@class_exists('blog')) if(file_exists("src/blog/controllers/blog.php")){
  include_once "src/blog/controllers/blog.php";
  new blog();
} else return;

$widget_data->n_post = @$widget_data->n_post?:4;
$widget_data->category = @$widget_data->category?:null;

foreach (core\models\post::getPosts(
    ['posts'=>$widget_data->n_post, 'category'=>$widget_data->category]) as $key=>$r ) {
  $href = blog::get_url($r['id'],$r['slug']);
  echo "<li>";
  echo "<a href='$href'>";
  if($key==0) {
    if($img=view::thumb_lg($r['img'])) {
      echo "<img src='$img'>";
    }
  }
  elseif($img=view::thumb_md($r['img'])) {
    echo "<img src='$img'>";
  }
  echo "</a><div><a href='$href' class='post-title'>{$r['title']}</a>";
  if($key==0) echo "<br>".($r['description']??$r['post']);
  echo "</div></li>";
}

?>
</ul>
