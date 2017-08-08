<ul class="widget-social-icons">
<?php
$social_icons = json_decode($widget_data->list,true);

foreach ($social_icons as $icon) {
  echo "<li><a href=\"{$icon['url']}\"><i class='fa fa-{$icon['icon']}' aria-hidden='true'></i></a></li>";
}

?>
</ul>
