<nav>
    <ul id="nav">
<?php
$mm = gila::menu();
//echo var_export($mm,true);
if ($mm) foreach ($mm as $mi) {
        if (!isset($mi['children'])) {
            echo "<li><a href=\"{$mi['url']}\">{$mi['title']}</a></li>";
        }
        else {
            echo "<li class=\"dropdown\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-expanded=\"false\">";
            echo "<a href=\"{$mi['url']}\" >{$mi['title']}</a>";
            echo "<ul class=\"dropdown-menu\" role=\"menu\">";
            foreach ($mi['children'] as $mii) {
                echo "<li><a href=\"{$mii['url']}\">{$mii['title']}</a></li>"; }
            echo "</ul></li>";
        }
} ?>
    </ul>
</nav>
