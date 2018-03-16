<ul class="g-nav">
<?php
use core\models\page as page;

$menu_items = $menu_data['children'];

foreach ($menu_items as $mi) {
    if (!isset($mi['children'])) {
        echo "<li>".menu_item($mi)."</li>";
    }
    else {
        echo "<li>";
        echo menu_item($mi);
        if (isset($mi['children'])) if (isset($mi['children'][0])) {
            echo "<ul class=\"dropdown-menu\" role=\"menu\">";
            foreach ($mi['children'] as $mii) {
                echo "<li>".menu_item($mii)."</li>";
            }
            echo "</ul></li>";
        }
    }
}

function menu_item($mi){
    global $db;
    $url = isset($mi['url'])?$mi['url']:'#';
    $name = isset($mi['name'])?$mi['name']:'';

    if($mi['type']=='page') {
        if($r=page::getById(@$mi['id'])){
            $url = $r['slug'];
            $name = $r['title'];
        }
    }
    if($mi['type']=='postcategory') {
        $ql = "SELECT id,title FROM postcategory WHERE id=?;";
        $res = $db->query($ql,@$mi['id']);
        while($r=mysqli_fetch_array($res)){
            $url = "category/".$r[0];
            $name = $r[1];
        }
    }
    if($mi['type']=='link') {

    }

    return "<a href=\"$url\" >$name</a>";
}
?>
</ul>
