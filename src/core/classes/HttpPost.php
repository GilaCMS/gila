<?php

namespace Gila;

class HttpPost
{
  private static $prefix = [];
  private $body;
  public $header = '';

  public function __construct($url, $data = [], $args = [], $name=null)
  {
    if (is_string($args)) {
      $name = $args;
      $args = [];
    }
    $content_type = $args['type'] ?? ($prefix[$name]['type']?? 'json');
    $ignore_error = $args['ignore_errors'] ?? ($prefix[$name]['ignore_errors']?? true);
    $headers = $args['headers'] ?? ($args['header'] ?? ($prefix[$name]['headers']?? []));
    if ($_data = @self::$prefix[$name]['data']) {
      $data = array_merge($_data, $data);
    }
    if ($_get = @self::$prefix[$name]['params']) {
      $q = http_build_query($_get);
      $url .= strpos($url, "?")? '&'.$q: '?'.$q;
    }
    if ($_url = @$data['url']) {
      $url = $_url.$url;
    }
    $header_str = '';
    foreach ($headers as $k=>$h) {
      $header_str .= $k.': '.$h."\r\n";
    }
    if (!isset($headers['content-type'])) {
      $header_str = "Content-type: application/$content_type\r\n".$header_str;
    }

    $options = [
      'http' => [
        'method'  => $args['method']??'POST',
        'header'  => $header_str,
        'content' => ($content_type==='json'? json_encode($data): http_build_query($data)),
        'ignore_errors' => $ignore_error
      ]
    ];

    $context = stream_context_create($options);
    $this->body = file_get_contents($url, false, $context);
    $this->header = $http_response_header;
  }

  public function body()
  {
    return $this->body;
  }

  public function json()
  {
    return json_decode($this->body) ?? null;
  }

  public function header($key=null)
  {
    if ($key===null) {
      return $this->header;
    }
    foreach ($this->header as $header) {
      if (strpos($header, $key)!==false) {
        $pos = strlen($key);
        if ($header[$pos]===':') {
          $pos++;
        }
        return trim(substr($header, $pos));
      }
    }
    return null;
  }

  public static function set($key, $prefixes)
  {
    self::$prefix[$key] = $prefixes;
  }
}

class_alias('HttpPost', 'gpost');
/* Examples
new HttpPost('https://hooks.slack.com/services/xxxxxxxx',['text'=>"Slack bot msg"]);
 */
