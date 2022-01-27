<?php

namespace templates\models;

use Gila\Config;
use Gila\View;
use Gila\Event;

class Email
{
  private static $template=[];

  public static function send($args)
  {
    global $db;

    $args['email'] = $args['email']?? Config::get('admin_email');
    if (!filter_var($args['email'], FILTER_VALIDATE_EMAIL)) {
      return;
    }

    $args['subject'] = $args['subject']?? "Message from ".Config::get('base');
    $args['headers'] = $args['headers']?? "From: ".Config::get('title')." <noreply@{$_SERVER['HTTP_HOST']}>";
    $args['message'] = $args['message']?? "";

    if (isset($args['event'])) {
      $args['template_id'] = self::templateId($args['event']);
    }

    if (isset($args['template_id'])) {
      $tid = $args['template_id'];
      if (!isset(self::$template[$tid])) {
        $temp = $db->getOne("SELECT * FROM `template` WHERE id=?;", [$tid]);
        $blocks = json_decode($temp['blocks'], true);
        $html = View::blocks($blocks, 'template_'.$tid);
        self::$template[$tid] = [
          'subject'=>$temp['title'],
          'html'=>$html
        ];
      }
      $translations = self::trData($args['data']);
      $args['subject'] = self::$template[$tid]['subject'];
      $args['html'] = self::$template[$tid]['html'];
      $args['subject'] = strtr($args['subject'], $translations);
      $args['html'] = strtr($args['html'], [
        '%7B%7B'=>'{{', '%7D%7D'=>'}}',
        'src="assets/'=>'src="'.Config::base().'assets/'
      ]);
      $args['html'] = strtr($args['html'], $translations);

      $args['message'] = $args['html'];
    }

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

  public static function trData($data, $prefix='')
  {
    $response = [];
    foreach ($data as $key=>$value) {
      if (is_array($value)) {
        $response = array_merge($response, self::trData($value, $key.'.'));
      } else {
        $response['{{'.$prefix.$key.'}}'] = $value;
        $response['<%'.$prefix.$key.'%>'] = $value;
      }
    }
    return $response;
  }

  public static function templateId($event)
  {
    global $db;
    $id = $db->value(
      "SELECT id FROM template WHERE `event`=? AND active=1 AND `language`=?;",
      [$event, Config::lang()]
    )??null;
    return $id;
  }
}
