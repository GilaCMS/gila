<?php

namespace Gila;

class UserAgent
{
  public static function info($user_agent = null)
  {
    if ($user_agent==null) {
      return null;
    }

    $device = 'SYSTEM';
    $os    = "Unknown OS";
    $os_array = [
      '/windows phone 8/i'    => 'Windows Phone 8',
      '/windows phone os 7/i' => 'Windows Phone 7',
      '/windows nt 6.3/i'     => 'Windows 8.1',
      '/windows nt 6.2/i'     => 'Windows 8',
      '/windows nt 6.1/i'     => 'Windows 7',
      '/windows nt 6.0/i'     => 'Windows Vista',
      '/windows nt 5.2/i'     => 'Windows Server 2003/XP x64',
      '/windows nt 5.1/i'     => 'Windows XP',
      '/windows xp/i'         => 'Windows XP',
      '/windows nt 5.0/i'     => 'Windows 2000',
      '/windows me/i'         => 'Windows ME',
      '/win98/i'              => 'Windows 98',
      '/win95/i'              => 'Windows 95',
      '/win16/i'              => 'Windows 3.11',
      '/macintosh|mac os x/i' => 'Mac OS X',
      '/mac_powerpc/i'        => 'Mac OS 9',
      '/linux/i'              => 'Linux',
      '/ubuntu/i'             => 'Ubuntu',
      '/iphone/i'             => 'iPhone',
      '/ipod/i'               => 'iPod',
      '/ipad/i'               => 'iPad',
      '/android/i'            => 'Android',
      '/blackberry/i'         => 'BlackBerry',
      '/webos/i'              => 'Mobile'
    ];

    foreach ($os_array as $regex => $value) {
      if (preg_match($regex, $user_agent)) {
        $os = $value;
        $device = !preg_match('/(windows|mac|linux|ubuntu)/i', $os)
                  ?'MOBILE':(preg_match('/phone/i', $os_platform)?'MOBILE':'SYSTEM');
      }
    }

    $browser = "Unknown Browser";
    $browser_array = [
      '/msie/i'       => 'IE',
      '/firefox/i'    => 'Firefox',
      '/safari/i'     => 'Safari',
      '/chrome/i'     => 'Chrome',
      '/opera/i'      => 'Opera',
      '/netscape/i'   => 'Netscape',
      '/maxthon/i'    => 'Maxthon',
      '/konqueror/i'  => 'Konqueror',
      '/mobile/i'     => 'Handheld Browser'
    ];
    foreach ($browser_array as $regex => $value) {
      if ($found) {
        break;
      }
      if (preg_match($regex, $user_agent, $result)) {
        $browser = $value;
      }
    }

    return ['os'=>$os, 'device'=>$device, 'browser'=>$browser];
  }

  public static function isBot($user_agent)
  {
    $good_bots = ['SemrushBot','YandexBot','AhrefsBot','PetalBot','SaaSHub','bingbot','BingPreview','MJ12bot','Twitterbot',
    'Googlebot','newspaper/0.2.8','NetcraftSurveyAgent','panscient.com','python-requests','SeznamBot','zgrab',
    'facebookexternalhit','Baiduspider','Nimbostratus-Bot','DotBot','DuckDuckBot','Slackbot-Link-Expanding',
    'Applebot','Python-urllib','bitlybot','TelegramBot', 'YandexMetrika', 'Go-http-client', 'DuckDuckGo-Favicons-Bot',
    'CensysInspect','Siteimprove','aiohttp','Barkrowler','AdsBot','Mediapartners-Google','aiHitBot'];

    if ($user_agent === null) return true;
    foreach ($good_bots as $bot) {
      if (strpos($user_agent, $bot) !== false) {
        return true;
      }
    }
    return false;
  }
}
