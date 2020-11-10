<base href="<?=Gila\Config::base()?>">
<title><?=$page_title?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="generator" content="Gila CMS">
<?php
  foreach (Gila\View::$meta as $key=>$value) {
    echo '<meta name="'.$key.'" content="'.htmlentities($value).'">';
  }
  Gila\Event::fire('head.meta');
  foreach (Gila\View::$stylesheet as $link) {
    Gila\View::$css[] = $link;
    echo '<link href="'.$link.'" rel="stylesheet">';
  }
  Gila\Event::fire('head');
  if (isset(Gila\View::$canonical)) {
    echo '<link rel="canonical" href="'.Gila\View::$canonical.'" />';
  }
  if (Gila\Config::get('favicon')) {
    echo '<link rel="icon" type="image/png" href="'.Gila\Config::get('favicon').'">';
  }
?>
