<?php global $g?>
<base href="<?=gila::base_url()?>">
<title><?=@$g->page_title?></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
  foreach(view::$meta as $key=>$value) echo '<meta name="'.$key.'" content="'.$value.'">';
  event::fire('head.meta');
  foreach(view::$stylesheet as $link) echo '<link href="'.$link.'" rel="stylesheet">';
  event::fire('head');
  if (isset(view::$canonical)) {
    echo '<link rel="canonical" href="'.view::$canonical.'" />';
  }
  if (gila::config('favicon')) {
    echo '<link rel="icon" type="image/png" href="'.gila::config('favicon').'">';
  }
?>
