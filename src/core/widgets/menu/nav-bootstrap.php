<ul id="nav" class="nav navbar-nav">
<?php
if (!isset($widget_data)) include 'default-data.php';

if (!is_array($widget_data->menu))
    $menu_items = json_decode($widget_data->menu,true);
else
    $menu_items = $widget_data->menu;


foreach ($menu_items as $mi) {
    if (isset($mi['children']) && isset($mi['children'][0]['url'])) {
        echo "<li class=\"dropdown\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-expanded=\"false\">";
        echo "<a href=\"{$mi['url']}\" >{$mi['title']}</a>";
        echo "<ul class=\"dropdown-menu\" role=\"menu\">";
        foreach ($mi['children'] as $mii) {
            echo "<li><a href=\"{$mii['url']}\">{$mii['title']}</a></li>"; }
        echo "</ul></li>";
    }
    else {
        echo "<li><a href=\"{$mi['url']}\">{$mi['title']}</a></li>";
    }
}
?>
</ul>
