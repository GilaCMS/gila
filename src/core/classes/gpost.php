<?php

class gpost
{
  private static $prefix = [];
  private $body;
  public $header = '';

  public function __construct($url, $args = [], $name=null) {

    $content_type = $args['type'] ?? ($prefix[$name]['type']?? 'json');
    $ignore_error = $args['ignore_errors'] ?? ($prefix[$name]['ignore_errors']?? true);
    if($_args = @self::$prefix[$name]['content']) $args = array_merge($_args, $args);
    if($_get = @self::$prefix[$name]['query']) {
      $q = http_build_query($_get);
      $url .= strpos($url,"?")? '&'.$q: '?'.$q;
    }

    $options = [
      'http' => [
        'method'  => 'POST',
        'header'  => "Content-type: application/$content_type\r\n",
        'content' => ($content_type=='json'? json_encode($args): http_build_query($args)),
        'ignore_errors' => $ignore_error
      ]
    ];

    $context  = stream_context_create($options);
    $this->body = file_get_contents($url, false, $context);
    $this->header = $http_response_header;
  }

  public function body() {
    return $this->body;
  }

  public function json() {
    return json_decode($this->body) ?? null;
  }

  public function header($key=null) {
    if($key==null) return $this->header;
    foreach($this->header as $header) if(strpos($header, $key)!==false) {
      $pos = strlen($key);
      if($header[$pos]==':') $pos++;
      return trim(substr($header, $pos));
    }
    return null;
  }

  public static function set($key, $prefixes) {
    self::$prefix[$key] = $prefixes;
  }
}

/* Examples
new gpost('https://hooks.slack.com/services/xxxxxxxx',['text'=>"Slack bot msg"]);
 */
