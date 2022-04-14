<?=View::cssAsync('lib/font-awesome/css/font-awesome.min.css')?>
<?=View::script('lib/bootstrap5/bootstrap.bundle.min.js')?>
<?=View::cssAsync('lib/bootstrap5/bootstrap.min.css')?>
<ul class="list-group list-group-horizontal">
<?php

$social_ = ['facebook','twitter','google','linkedin','pinterest','youtube','instagram','newsletter','medium','tumblr','github','codepen','twitch','slack','stack-overflow','vk','rss','soundcloud'];
$social_icons = ['newsletter'=>'envelope'];

foreach ($social_ as $s) {
  if (isset($widget_data->$s)) {
    if ($widget_data->$s!='') {
      $icon = $social_icons[$s] ?? $s;
      echo "<li class=\"list-group-item me-2 bg-dark social-{$s}\"><a class='link-light' href=\"".htmlentities($widget_data->$s)."\" target=\"_blank\"><i class='fa fa-{$icon}' aria-hidden='true'></i></a></li>";
    }
  }
}

?>
</ul>
