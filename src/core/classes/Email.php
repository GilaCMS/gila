<?php

namespace Gila;

use Gila\Config;
use Gila\View;
use Gila\Event;

class Email
{
  private static $template=[];

  public static function send($args)
  {
    $args['email'] = $args['email']?? Config::get('admin_email');
    if (!filter_var($args['email'], FILTER_VALIDATE_EMAIL)) {
      return;
    }

    $args['subject'] = $args['subject']?? "Message from ".Config::get('base');
    $server = $_SERVER['HTTP_HOST'] ?? '';
    $args['headers'] = $args['headers']?? "From: ".Config::get('title')." <noreply@$server>";
    $args['message'] = $args['message']?? "";

    if (isset($args['from'])) {
      $name = $args['from']['name'] ?? Config::get('title');
      $email = $args['from']['email'];
      $args['headers'] = "From: $name <$email>";
    }

    if (isset($args['event'])) {
      $args['template_id'] = self::templateId($args['event'], $args['language'] ?? Config::lang());
    }

    if (isset($args['template_id'])) {
      self::useTemplate($args);
    }

    if ($args['message']==="" && is_array($args['post'])) {
      foreach ($args['post'] as $key) {
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

  public static function templateId($event, $lang=null)
  {
    $id = DB::value(
      "SELECT id FROM template WHERE `event`=? AND active=1 AND `language`=?;",
      [$event, $lang ?? Config::lang()]
    )??null;
    return $id;
  }

  public static function sendTo($to, $args)
  {
    if (!is_array($to)) {
      $to = [$to];
    }
    if (Event::get('sendTo', false, $args)===false) {
      return;
    }
    $data = $args['data'] ?? [];
    foreach ($to as $user) {
      $args['email'] = $user['email'] ?? $user;
      $args['data'] = $user['data'] ?? $data;
      self::send($args);
    }
  }

  public static function useTemplate(&$args)
  {
    $tid = $args['template_id'];
    if (!isset(self::$template[$tid])) {
      $temp = DB::getOne("SELECT * FROM `template` WHERE id=?;", [$tid]);
      $blocks = json_decode($temp['blocks'], true);
      View::$blockSections = false;
      $html = View::blocks($blocks, 'template_'.$tid);
      View::$blockSections = true;
      self::$template[$tid] = [
        'subject'=>$temp['title'],
        'html'=>$html
      ];
      file_put_contents('log/email0.txt', $html);
      file_put_contents('log/email-blocks.txt', $temp['blocks']);
    }
    
    $translations = self::trData($args['data']);
    $args['subject'] = self::$template[$tid]['subject'];
    $args['html'] = self::$template[$tid]['html'];
    file_put_contents('log/email1.txt', $args['html']);
    $args['subject'] = strtr($args['subject'], $translations);
    $args['html'] = strtr($args['html'], [
      '%7B%7B'=>'{{', '%7D%7D'=>'}}',
      'src="assets/'=>'src="'.Config::base().'assets/'
    ]);
    file_put_contents('log/email2.txt', $args['html']);
    $args['html'] = strtr($args['html'], $translations);

    $args['message'] = $args['html'];
    file_put_contents('log/email3.txt', $args['message']);
  }
}
