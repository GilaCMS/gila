<?php

class sendmail
{

  function __construct($args)
  {
    $args['email'] = $args['email']?? gila::config('admin_email');
    $args['subject'] = $args['subject']?? "Message from ".gila::config('base');
    $args['headers'] = $args['headers']?? "From: GilaCMS <noreply@{$_SERVER['HTTP_HOST']}>";
    $args['message'] = $args['message']?? "";
    if($args['message']=="") foreach(@$args['post'] as $key) {
      $args['message'] .= "$key:\n".htmlentities($_POST[$key])."\n\n";
    }
    if(!event::get('sendmail', $args)) {
      mail($args['email'], $args['subject'], $args['message'], $args['headers']);
    }
  }
}
