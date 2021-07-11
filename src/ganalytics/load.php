<?php

Event::listen('head', function () {
  if (Router::getController()=='blocks') {
    return;
  }
  $trackingID = Config::get('ganalytics.trackingID');
  if ($trackingID=='') {
    return;
  } ?>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?=$trackingID?>"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '<?=$trackingID?>');
    </script>
    <?php
});
