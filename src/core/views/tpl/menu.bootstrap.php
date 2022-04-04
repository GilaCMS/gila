<ul class="nav navbar-nav navbar-right">
<?php
use Gila\Page;
use Gila\DB;

$menu_items = $menu_data['children'];

foreach ($menu_items as $mi) {
  if (!isset($mi['children'])) {
    echo "<li>".menu_item($mi)."</li>";
  } else {
    echo "<li>";
    if (isset($mi['children'])) {
      if (isset($mi['children'][0])) {
        echo "<li class=\"dropdown\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-expanded=\"false\">";
        echo menu_item($mi);
        echo "<ul class=\"dropdown-menu\" role=\"menu\">";
        foreach ($mi['children'] as $mii) {
          echo "<li>".menu_item($mii)."</li>";
        }
        echo "</ul></li>";
      }
    }
  }
}

function menu_item($mi)
{
  $url = isset($mi['url'])?$mi['url']:(Router::path().'#');
  $name = isset($mi['name'])?$mi['name']:'';

  if ($mi['type']=='page') {
    if ($r=Page::getById(@$mi['id'])) {
      $url = $r['slug'];
      $name = $r['title'];
    }
  }
  if ($mi['type']=='postcategory') {
    $ql = "SELECT id,title FROM postcategory WHERE id=?;";
    $res = DB::query($ql, @$mi['id']);
    while ($r=mysqli_fetch_array($res)) {
      $url = "category/".$r[0];
      $name = $r[1];
    }
  }
  if ($mi['type']=='link') {
  }
  if ($res = MenuItemTypes::get($mi)) {
    list($url, $name) = $res;
  }

  return "<a href=\"$url\" >$name</a>";
}
?>
</ul>
