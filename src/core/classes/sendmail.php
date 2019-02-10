<?php

class sendmail
{

  function __construct($args)
  {
    $email = $args['email']?? gila::config('admin_email');
    $subject = $args['subject']?? "Message from ".gila::config('base');
    $headers = $args['headers']?? "From: GilaCMS <noreply@{$_SERVER['HTTP_HOST']}>";
    $message = $args['message']?? "";
    if($message=="") foreach($_POST as $key=>$post) {
      $message .= "$key:\n$post\n\n";
    }
    mail($email, $subject, $message, $headers);
  }
}
