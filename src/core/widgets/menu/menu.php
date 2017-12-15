<ul class="g-nav">
    <li><a href="">Home</a></li>
<?php

global $db;

$menu_items = json_decode($widget_data->menu,true);

$ql = "SELECT id,title,slug FROM page WHERE publish=1;";
$pages = $db->get($ql);

    foreach ($pages as $p) {
            echo "<li><a href=\"{$p[2]}\">{$p[1]}</a></li>";
    }

 ?>
</ul>
<?php return; ?>

<ul class="g-nav">
<?php
$menu_items = json_decode($widget_data->menu,true);


    foreach ($menu_items as $mi) {
            if (!isset($mi['children'])) {
                echo "<li><a href=\"{$mi['url']}\">{$mi['title']}</a></li>";
            }
            else {
                echo "<li>";
                echo "<a href=\"{$mi['url']}\" >{$mi['title']}</a>";
                if (isset($mi['children'][0])) if (isset($mi['children'][0][0])) {
                    echo "<ul class=\"dropdown-menu\" role=\"menu\">";
                    foreach ($mi['children'][0] as $mii) if(isset($mii['url'])){
                        echo "<li><a href=\"{$mii['url']}\">{$mii['title']}</a></li>";
                    }
                    echo "</ul></li>";
                }
            }
    }

 ?>
</ul>
