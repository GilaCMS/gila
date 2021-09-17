<?php

if (!class_exists('Website') || Website::getPlanId()>1) {
  Config::widgets([
    'embed-code'=>'embed-code/widgets/embed-code'
  ]);
  
  Event::listen('footer', function () {
    echo '<script>'.Config::get('embed-code.code-snippet').'</script>';
    if ($_SERVER['HTTP_USER_AGENT']!='188.213.25.21') {
      echo '<script>document.querySelector("meta[http-equiv=\'refresh\']")?.remove()</script>';
    }
  });
}
