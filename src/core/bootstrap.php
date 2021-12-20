<?php

use Gila\Config;
use Gila\Router;
use Gila\Event;
use Gila\Db;
use Gila\Session;
use Gila\Logger;

$starttime = microtime(true);
function timeDebug($txt)
{
  global $starttime;
  $end = microtime(true);
  $log = new Logger(LOG_PATH.'/timeDebug.log');
  $log->log(round($end-$starttime, 6), $txt, ['uri'=>$_GET['p']??'']);
  $starttime = $end;
}

$site_folder = 'sites/'.$_SERVER['HTTP_HOST'];
if (file_exists($site_folder)) {
  define('SITE_PATH', $site_folder.'/');
  define('LOG_PATH', $site_folder.'/log');
  define('TMP_PATH', $site_folder.'/tmp');
  define('CONFIG_PHP', $site_folder.'/config.php');
  define('FS_ACCESS', false);
} else {
  define('SITE_PATH', '');
  define('LOG_PATH', 'log');
  define('TMP_PATH', 'tmp');
  define('CONFIG_PHP', 'config.php');
  define('FS_ACCESS', true);
}

if (!isset($_GET['p'])) {
  $_GET['p'] = substr($_SERVER['REQUEST_URI'], 1);
}

ini_set("error_log", "log/error.log");

$classMap = include __DIR__.'/classmap.php';

spl_autoload_register(function ($class) {
  global $classMap;
  if (isset($classMap[$class])) {
    require_once $classMap[$class];
    return true;
  }

  $class = strtr($class, ['\\'=>'/', '__'=>'-']);

  if (file_exists('src/core/classes/'.$class.'.php')) {
    require_once 'src/core/classes/'.$class.'.php';
    class_alias('Gila\\'.$class, $class);
  } elseif (file_exists('src/'.$class.'.php')) {
    require_once 'src/'.$class.'.php';
  } elseif (in_array($class, ['core/models/Post', 'core/models/Page', 'core/models/Widget', 'core/models/User', 'core/models/Profile'])) {
    // DEPRECATED
    $d = debug_backtrace()[1];
    error_log("Use namespace: Gila\\$class (".$d['file'].' line '.$d['line'].')', 3, 'log/error.log');
    require_once 'src/core/classes/'.substr($class, 12).'.php';
    class_alias('Gila\\'.substr($class, 12), strtr($class, ['/'=>'\\']));
  }
});

if (file_exists('vendor/autoload.php')) {
  include_once 'vendor/autoload.php';
}

$GLOBALS['config'] = [];
@include_once CONFIG_PHP;
if ($GLOBALS['config'] === []) {
  include 'src/core/install/index.php';
  exit;
}


if (Config::getArray('trusted_domains') &&
    isset($_SERVER['HTTP_HOST']) &&
    !in_array($_SERVER['HTTP_HOST'], Config::get('trusted_domains')) &&
    strpos(Config::get('base'), $_SERVER['HTTP_HOST'])===false) {
  header('Location: '.Config::get('base').substr($_SERVER['REQUEST_URI'], 1));
}

$db = new Db($GLOBALS['config']['db']);

if (!@include LOG_PATH.'/load.php') {
  Config::load();
  Package::updateLoadFile();
}
@include SITE_PATH.'/load.php';

if (Config::get('env') == 'dev') {
  error_reporting(E_ALL);
  ini_set('display_errors', '1');
  ini_set('display_startup_errors', '1');
} else {
  error_reporting(E_ERROR);
  ini_set('display_errors', 0);
  ini_set('display_startup_errors', 0);
}

Event::fire('load');

function __($key, $alt = null)
{
  return Config::tr($key, $alt);
}

$theme = Config::get('theme');
if (isset($_GET['g_preview_theme']) && Session::hasPrivilege('admin')) {
  $gtheme = strtr($_GET['g_preview_theme'], ['.'=>'','\\'=>'','/'=>'']);
  if (file_exists('themes/'.$gtheme)) {
    $theme = $gtheme;
  }
}
if (file_exists("themes/$theme/load.php")) {
  include "themes/$theme/load.php";
}
if ($cors = Config::getArray('cors')) {
  foreach ($cors as $url) {
    @header('Access-Control-Allow-Origin: '.$url);
  }
}

Router::run($_GET['p'] ?? '');
