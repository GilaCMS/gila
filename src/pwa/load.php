<?php

/*
<link rel="mask-icon" href="/favicon/safari-pinned-tab.svg" color="#ff9900">
*/


event::listen('head', function(){ ?>
<link rel="apple-touch-icon" sizes="144x144" href="<?=view::thumb($icon,'fav/144_',144)?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?=view::thumb($icon,'fav/32_',32)?>">
<link rel="icon" type="image/png" sizes="16x16" href="<?=view::thumb($icon,'fav/16_',16)?>">
<link rel="manifest" href="manifest.json">
<script>
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('service-worker.js');
}
</script>
<?php });

gila::route('manifest.json',function(){
    $icon = gila::option('pwa.icon','gila-logo.png');
    include "src/pwa/manifest.php";
    exit;
});

gila::route('service-worker.js',function(){
    header('Content-type: text/javascript');
    include "src/pwa/service-worker.js";
    exit;
});
