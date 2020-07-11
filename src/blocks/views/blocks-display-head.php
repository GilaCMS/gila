<head>
<base href="<?=Gila::base_url()?>">
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

  if (Gila::config('favicon')) {
    echo '<link rel="icon" type="image/png" href="'.Gila::config('favicon').'">';
  }
?>
<head>
