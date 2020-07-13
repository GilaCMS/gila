<ul class="g-nav">
<?php
use Gila\Page;

$menu_items = $menu_data['children'];

foreach ($menu_items as $mi) {
  if (!isset($mi['children'])) {
    echo menu_item($mi, 'li')."</li>";
  } else {
    echo menu_item($mi);
    if (isset($mi['children'])) {
      if (isset($mi['children'][0])) {
        echo "<ul class=\"dropdown-menu\" role=\"menu\">";
        foreach ($mi['children'] as $mii) {
          echo menu_item($mii)."</li>";
        }
        echo "</ul></li>";
      }
    }
  }
}

function menu_item($mi, $tag='')
{
  global $db;

  $url = isset($mi['url'])?$mi['url']:(Router::url().'#');
  $name = isset($mi['name'])?$mi['name']:'';

  if ($mi['type']=='page') {
    if ($r=Page::getById(@$mi['id'])) {
      $url = $r['slug'];
      $name = $r['title'];
    }
  }
  if ($mi['type']=='postcategory') {
    $ql = "SELECT id,title,slug FROM postcategory WHERE id=?;";
    $res = $db->query($ql, @$mi['id']);
    while ($r=mysqli_fetch_array($res)) {
      $url = "category/".$r[0].'/'.$r[2];
      $name = $r[1];
    }
  }
  if ($mi['type']=='widget') {
    echo '<li><a href=\"'.$url.'\" >'.$mi['name'].'</a><ul style="min-width:240px">';
    Gila\View::widgetBody(@$mi['widget']);
    echo '</ul>';
    return;
  }
  if ($mi['type']=='link') {
  }

  if ($res = MenuItemTypes::get($mi)) {
    list($url, $name) = $res;
  }

  if (Router::url()==$url) {
    return "<li class=\"active\"><a href=\"$url\">$name</a>";
  }
  return "<li><a href=\"$url\" >$name</a>";
}
?>
</ul>
