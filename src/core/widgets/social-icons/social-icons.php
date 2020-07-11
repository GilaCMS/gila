<?=View::cssAsync('lib/font-awesome/css/font-awesome.min.css')?>
<style>
.widget-social-icons {list-style: none;padding:0; display: inline-block}
.widget-social-icons li{margin: 15px 10px 0 0; float: left; text-align: center; opacity: 0.8}
.widget-social-icons li a i:before{
  width: 40px;
  margin: 0;
  color: #fff;
  font-size: 20px;
  line-height: 40px;
  display:inline-block;
  background: #060608;
}
</style>

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
