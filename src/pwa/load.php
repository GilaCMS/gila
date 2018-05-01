<?php

/*
<link rel="apple-touch-icon" sizes="144x144" href="/favicon/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
<link rel="mask-icon" href="/favicon/safari-pinned-tab.svg" color="#ff9900">
*/



event::listen('head', function(){ ?>
<link rel="manifest" href="/favicon/manifest.json">
<script>
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('service-worker.js');
}
</script>
<?php });

gila::route('manifest.json',function(){
    $icon = gila::option('gila_pwa.icon','gila-logo.png');
    include "src/gila_pwa/manifest.php";
    exit;
});

gila::route('service-worker.js',function(){
    include "src/gila_pwa/service-worker.js";
    exit;
});
