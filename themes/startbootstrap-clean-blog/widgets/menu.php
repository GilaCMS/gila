<ul class="nav navbar-nav navbar-right">
<?php
$menu_items = json_decode($widget_data,true);

foreach ($menu_items as $mi) {
        if (!isset($mi['children'])) {
            echo "<li><a href=\"{$mi['url']}\">{$mi['title']}</a></li>";
        }
        else {
            echo "<li>";
            echo "<a href=\"{$mi['url']}\" >{$mi['title']}</a>";
            echo "<ul class=\"dropdown-menu\" role=\"menu\">";
            foreach ($mi['children'] as $mii) {
                echo "<li><a href=\"{$mii['url']}\">{$mii['title']}</a></li>"; }
            echo "</ul></li>";
        }
} ?>
</ul>
