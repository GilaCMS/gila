<?=View::cssAsync('lib/font-awesome/css/font-awesome.min.css')?>
<?=View::script('lib/bootstrap5/bootstrap.bundle.min.js')?>
<?=View::cssAsync('lib/bootstrap5/bootstrap.min.css')?>
<?php
  $social_ = ['facebook','twitter','google','linkedin','pinterest','youtube','instagram','newsletter','medium','tumblr','github','codepen','twitch','slack','stack-overflow','vk','rss','soundcloud'];
  $social_icons = ['newsletter'=>'envelope'];

  $latte = new Latte\Engine;
  $latte->setTempDirectory(__DIR__."/../../latteTemp");
  $params = [
    'widget_data' => $widget_data,
    'social_' => $social_,
    'social_icons' => $social_icons,
  ];
  // render to output
  $latte->render(__DIR__.'/widget.latte', $params);
?>

