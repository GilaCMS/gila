<ul class="g-nav">
<?php

if(@$widget_data == null) include __DIR__.'/default-data.php';

if(!is_array($widget_data->menu)) {
    $menu_items = json_decode($widget_data->menu,true);
} else {
    $menu_items = $widget_data->menu;
}

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
