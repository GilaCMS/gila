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

    $postdata = http_build_query(
        array(
            'secret' => $secret,
            'response' => $_POST['g-recaptcha-response']
        )
    );

    $opts = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata
        )
    );

    $context = stream_context_create($opts);
    $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
    return json_decode($response)->success;
});
