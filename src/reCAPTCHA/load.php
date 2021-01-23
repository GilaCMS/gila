<?php

Event::listen('recaptcha.form', function () {
  if ($sitekey = Config::get('reCAPTCHA.site_key')) {
    View::script('https://www.google.com/recaptcha/api.js');
    echo '<div class="g-recaptcha" data-sitekey="'.$sitekey.'"></div>';
  }
});

Event::listen('recaptcha', function () {
  $secret = Config::get('reCAPTCHA.secret_key');
  if ($secret===null) {
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
