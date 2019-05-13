<?php

event::listen('sendmail', function($args){
  $respose = new gpost(
    "https://api.pepipost.com/v2/sendEmail",
    ['personalizations'=>[
      'recipient'=> $args['email'],
      'fromEmail'=> 'noreply@'.$_SERVER['HTTP_HOST'],
      'subject'=> $args['subject'],
      'content'=> $args['message']
    ]],
    ['header'=>[
      'api_key'=> gila::option('sendmail-pepipost.apiKey')
    ]]
  );
});
