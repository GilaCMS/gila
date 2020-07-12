<base href="<?=Gila\Gila::base_url()?>">
<title><?=$page_title?></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="generator" content="Gila CMS">
<?php
  foreach (Gila\View::$meta as $key=>$value) {
    echo '<meta name="'.$key.'" content="'.$value.'">';
  }
  Event::fire('head.meta');
  foreach (Gila\View::$stylesheet as $link) {
    echo '<link href="'.Gila\Gila::base_url($link).'" rel="stylesheet">';
  }
  Event::fire('head');
  if (isset(Gila\View::$canonical)) {
    echo '<link rel="canonical" href="'.Gila\View::$canonical.'" />';
  }
  if (Gila\Gila::config('favicon')) {
    echo '<link rel="icon" type="image/png" href="'.Gila\Gila::config('favicon').'">';
  }
?>
