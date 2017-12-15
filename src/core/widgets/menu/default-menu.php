<ul class="g-nav">
    <li><a href="">Home</a></li>
<?php

global $db;

$menu_items = json_decode($widget_data->menu,true);

$ql = "SELECT id,title,slug FROM page WHERE publish=1;";
$pages = $db->list($ql);

    foreach ($pages as $p) {
            echo "<li><a href=\"{$p[2]}\">{$p[1]}</a></li>";
    }

 ?>
</ul>
