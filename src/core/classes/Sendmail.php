<?php

namespace Gila;

class Sendmail
{
  public function __construct($args)
  {
    $args['email'] = $args['email']?? Config::get('admin_email');
    $args['subject'] = $args['subject']?? "Message from ".Config::get('base');
    $args['headers'] = $args['headers']?? "From: GilaCMS <noreply@{$_SERVER['HTTP_HOST']}>";
    $args['message'] = $args['message']?? "";
    if ($args['message']==="") {
      foreach (@$args['post'] as $key) {
        $args['message'] .= "$key:\n".htmlentities($_POST[$key])."\n\n";
      }
    }
    if (Event::get('sendmail', false, $args)===false) {
      mail($args['email'], $args['subject'], $args['message'], $args['headers']);
    }
  }
}
