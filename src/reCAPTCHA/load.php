<?php

event::listen('recaptcha.form',function(){
	$sitekey = gila::option('reCAPTCHA.site_key');
    if($sitekey=='') return;
    ?>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <div class="g-recaptcha" data-sitekey="<?=$sitekey?>"></div>
	<?php
});

event::listen('recaptcha',function(){
  $secret = gila::option('reCAPTCHA.secret_key');
  if($secret=='') return false;
  if(!isset($_POST['g-recaptcha-response'])) return false;

  $response = new gpost('https://www.google.com/recaptcha/api/siteverify',
    ['secret' => $secret, 'response' => $_POST['g-recaptcha-response']],
    ['type'=>'x-www-form-urlencoded']);
  return json_decode($response)->success;
});
