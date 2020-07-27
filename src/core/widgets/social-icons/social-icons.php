<?=View::cssAsync('lib/font-awesome/css/font-awesome.min.css')?>
<?=View::css('core/widgets.css')?>
<ul class="widget-social-icons">
<?php

$social_ = ['facebook','twitter','google','linkedin','pinterest','youtube','instagram','medium','tumblr','github','codepen','twitch','slack','stack-overflow','vk','rss','soundcloud'];

foreach ($social_ as $s) {
  if (isset($widget_data->$s)) {
    if ($widget_data->$s!='') {
      echo "<li class=\"social-{$s}\"><a href=\"".htmlentities($widget_data->$s)."\" target=\"_blank\"><i class='fa fa-{$s}' aria-hidden='true'></i></a></li>";
    }
  }
}

?>
</ul>
