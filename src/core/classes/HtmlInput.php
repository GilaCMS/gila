<?php
namespace Gila;

class HtmlInput
{
  public static $eventAttributes;

  public static function purify($value, $allowed_tags=false)
  {
    if ($allowed_tags===false) {
      return strip_tags($value);
    }
    if ($allowed_tags!==true) {
      $value = strip_tags($value, $allowed_tags);
    }

    if ($response = Event::get('HtmlInput::purify', null, $value)) {
      return $response;
    }
    
    if (empty(trim($value))) {
      $value = '';
    } elseif (class_exists("DomDocument")) {
      $value = self::DOMSanitize($value);
    }

    $tD = 'javascript&#8282;';
    $value = strtr($value, ['javascript&#x3a;'=>$tD,'javascript&#58;'=>$tD,'javascript&colon;'=>$tD,'javascript:'=>$tD]);
    $meta_tags = self::getMetaTags($value);
    if (isset($meta_tags['refresh'])) {
      foreach ($meta_tags['refresh'] as $content) {
        $value=strtr($value, [$content=>'']);
      }
    }

    return $value;
  }

  public static function DOMSanitize($value, $js=true)
  {
    // TODO: remove specific style attributes like box-sizing
    $dom = new \DOMDocument;
    @$dom->loadHTML('<?xml encoding="utf-8"?>'.$value);

    if ($js) {
      $tags = $dom->getElementsByTagName('script');
      foreach (iterator_to_array($tags) as $tag) {
        $tag->parentNode->removeChild($tag);
      }
      $tags = $dom->getElementsByTagName('*');
      foreach ($tags as $tag) {
        foreach (self::$eventAttributes as $attr) {
          $tag->removeAttribute($attr);
        }
      }
    }

    $body = $dom->getElementsByTagName('body')->item(0);
    $value = $dom->saveHTML($body);
    $value = strtr($value, ['<body>'=>'', '</body>'=>'']);

    if (substr($value, 1, 8)=='<p> </p>') {
      $value = substr($value, 8);
    }
    return $value;
  }

  public static function getMetaTags($str)
  {
    $pattern = '
~<\s*meta\s

  (?=[^>]*?
  \b(?:name|property|http-equiv)\s*=\s*
  (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
  ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
)

[^>]*?\bcontent\s*=\s*
  (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
  ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
[^>]*>

~ix';
    if (preg_match_all($pattern, $str, $out)) {
      return array_fill_keys($out[1], $out[2]);
    }
    return array();
  }
}

HtmlInput::$eventAttributes = [
  'onafterprint',
  'onbeforeprint',
  'onbeforeunload',
  'onerror',
  'onhashchange',
  'onload',
  'onmessage',
  'onoffline',
  'ononline',
  'onpagehide',
  'onpageshow',
  'onpopstate',
  'onresize',
  'onstorage',
  'onunload',
  'onblur',
  'onchange',
  'onfocus',
  'oninput',
  'oninvalid',
  'onreset',
  'onsearch',
  'onselect',
  'onsubmit',
  'onkeydown',
  'onkeypress',
  'onkeyup',
  'onclick',
  'ondblclick',
  'onmousedown',
  'onmousemove',
  'onmouseout',
  'onmouseover',
  'onmouseup',
  'onmousewheel',
  'onwheel',
  'oncopy',
  'oncut',
  'onpaste',
  'onabort',
  'oncanplay',
  'oncanplaythrough',
  'oncuechange',
  'ondurationchange',
  'onemptied',
  'onended',
  'onerror',
  'onloadeddata',
  'onloadedmetadata',
  'onloadstart',
  'onpause',
  'onplay',
  'onplaying',
  'onprogress',
  'onratechange',
  'onseeked',
  'onseeking',
  'onstalled',
  'onsuspend',
  'ontimeupdate',
  'onvolumechange',
  'onwaiting'
];
