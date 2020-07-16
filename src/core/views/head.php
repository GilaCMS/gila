<base href="<?=Config::base_url()?>">
<title><?=$page_title?></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="generator" content="Gila CMS">
<?php
  foreach (View::$meta as $key=>$value) {
    echo '<meta name="'.$key.'" content="'.$value.'">';
  }
 Event::fire('head.meta');
  foreach (View::$stylesheet as $link) {
    echo '<link href="'.Config::base_url($link).'" rel="stylesheet">';
  }
 Event::fire('head');
  if (isset(View::$canonical)) {
    echo '<link rel="canonical" href="'.View::$canonical.'" />';
  }
  if (Config::config('favicon')) {
    echo '<link rel="icon" type="image/png" href="'.Config::config('favicon').'">';
  }
?>
