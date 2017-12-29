<?php

event::listen('head',function(){
    $trackingID = gila::option('ganalytics.trackingID');
    if($trackingID=='') return;
    ?>
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
