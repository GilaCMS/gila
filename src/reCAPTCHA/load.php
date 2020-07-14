<?php

Event::listen('recaptcha.form', function () {
  $sitekey = Config::option('reCAPTCHA.site_key');
  if ($sitekey=='') {
    return;
  } ?>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <div class="g-recaptcha" data-sitekey="<?=$sitekey?>"></div>
	<?php
});

Event::listen('recaptcha', function () {
  $secret = Config::option('reCAPTCHA.secret_key');
  if ($secret=='') {
    return false;
  }
  if (!isset($_POST['g-recaptcha-response'])) {
    return false;
  }

  $response = new HttpPost(
    'https://www.google.com/recaptcha/api/siteverify',
    ['secret' => $secret, 'response' => $_POST['g-recaptcha-response']],
    ['type'=>'x-www-form-urlencoded']
  );
  return json_decode($response)->success;
});
