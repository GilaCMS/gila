<?php

Gila\Event::listen('recaptcha.form', function () {
  if ($sitekey = Config::get('reCAPTCHA.site_key')) {
    Gila\View::script('https://www.google.com/recaptcha/api.js');
    echo '<div class="g-recaptcha" data-sitekey="'.$sitekey.'"></div>';
  }
});

Gila\Event::listen('recaptcha', function () {
  $secret = Gila\Config::get('reCAPTCHA.secret_key');
  if ($secret===null) {
    return false;
  }
  if (!isset($_POST['g-recaptcha-response'])) {
    return false;
  }
  $remoteip = $_SERVER['HTTP_X_FORWARDED_FOR']??$_SERVER['REMOTE_ADDR'];

  $response = new Gila\HttpPost(
    'https://www.google.com/recaptcha/api/siteverify',
    ['secret' => $secret, 'response' => $_POST['g-recaptcha-response'], 'remoteip'=>$remoteip],
    ['type'=>'x-www-form-urlencoded']
  );
  return json_decode($response->body())->success;
});
