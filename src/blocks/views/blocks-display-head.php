<head>
<base href="<?=Config::base_url()?>">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
  foreach (View::$meta as $key=>$value) {
    echo '<meta name="'.$key.'" content="'.$value.'">';
  }
 Event::fire('head.meta');
  foreach (View::$stylesheet as $link) {
    echo '<link href="'.$link.'" rel="stylesheet">';
  }
 Event::fire('head');

  if (Config::config('favicon')) {
    echo '<link rel="icon" type="image/png" href="'.Config::config('favicon').'">';
  }
?>
<head>
