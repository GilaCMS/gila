<head>
<base href="<?=Gila\Gila::base_url()?>">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
  foreach (Gila\View::$meta as $key=>$value) {
    echo '<meta name="'.$key.'" content="'.$value.'">';
  }
 Gila\Event::fire('head.meta');
  foreach (Gila\View::$stylesheet as $link) {
    echo '<link href="'.$link.'" rel="stylesheet">';
  }
 Gila\Event::fire('head');

  if (Gila\Gila::config('favicon')) {
    echo '<link rel="icon" type="image/png" href="'.Gila\Gila::config('favicon').'">';
  }
?>
<head>
