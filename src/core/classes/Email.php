<?php

namespace Gila;

class Sendmail
{
  public function __construct($args)
  {
    $args['email'] = $args['email']?? Config::get('admin_email');
    if (!filter_var($args['email'], FILTER_VALIDATE_EMAIL)) {
      return;
    }
    $args['subject'] = $args['subject']?? "Message from ".Config::get('base');
    $args['headers'] = $args['headers']?? "From: GilaCMS <noreply@{$_SERVER['HTTP_HOST']}>";
    $args['message'] = $args['message']?? "";
    if ($args['message']==="") {
      foreach (@$args['post'] as $key) {
        $label = is_array($key)? $key[1]: $key;
        $value = is_array($key)? $_POST[$key[0]]: $_POST[$key];
        $args['message'] .= "$label:\n".htmlentities($value)."\n\n";
      }
    }
    if (Event::get('sendmail', false, $args)===false) {
      mail($args['email'], $args['subject'], $args['message'], $args['headers']);
    }
  }
}
