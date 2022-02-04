<ul class="g-nav g-pagination pagination">
<?php
  $url = htmlspecialchars(explode('?', Router::path())[0]);
  if (Config::lang()!==Config::get('language')) {
    $url = Config::lang().'/'.$url;
  }
  $page = $_GET['page']??1;
  $totalpages = $totalpages??ceil((Post::total())/12);
  if ($page>5) {
    echo '<li><a href="'.$url.'?page=1">1</a></li>';
  }
  if ($page>6) {
    echo ' .. ';
  }
  $r=3;
  if ($totalpages>1) {
    $r = 3;
    for ($pl=$page-$r-1;$pl<$page+$r+1;$pl++) {
      if ($pl>0 && $pl<=$totalpages) {
        $active = "";
        if ($pl==$page) {
          $active="active";
        } ?>
        <li class="<?=$active?>">
          <a href="<?=$url?>?page=<?=$pl?>"><?=$pl?></a>
        </li>
      <?php
      }
    }
  }
  if ($page<$totalpages-6) {
    echo ' .. ';
  }
  if ($page<$totalpages-5) {
    echo '<li><a href="'.$url.'?page='.$totalpages.'">'.$totalpages.'</a></li>';
  }
?>
</ul>
