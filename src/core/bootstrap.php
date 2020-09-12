<?php

use Gila\Config;
use Gila\Router;
use Gila\Event;
use Gila\Db;
use Gila\Session;

$starttime = microtime(true);
function timeDebug($txt)
{
  global $starttime;
  $end = microtime(true);
  printf("<br>".$txt." %.6f seconds.", $end - $starttime);
  $starttime = $end;
}

$site_folder = 'sites/'.$_SERVER['HTTP_HOST'];
if (file_exists($site_folder)) {
  define('SITE_PATH', $site_folder.'/');
  define('LOG_PATH', $site_folder.'/log');
  define('CONFIG_PHP', $site_folder.'/config.php');
  define('FS_ACCESS', false);
} else {
  define('SITE_PATH', '');
  define('LOG_PATH', 'log');
  define('CONFIG_PHP', 'config.php');
  define('FS_ACCESS', true);
}

if (!isset($_GET['url'])) {
  $_GET['url'] = substr($_SERVER['REQUEST_URI'], 1);
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
  $Class = ucfirst($class);

  if (file_exists('src/core/classes/'.$class.'.php')) {
    require_once 'src/core/classes/'.$class.'.php';
    class_alias('Gila\\'.$class, $class);
  } elseif (file_exists('src/core/classes/'.$Class.'.php')) {
    trigger_error("Class name $Class is capitalized", E_USER_WARNING);
    require_once 'src/core/classes/'.$Class.'.php';
    class_alias('Gila\\'.$Class, $class);
  } elseif (file_exists('src/'.$class.'.php')) {
    require_once 'src/'.$class.'.php';
  } elseif (in_array($class, ['core/models/Post', 'core/models/Page', 'core/models/Widget', 'core/models/User', 'core/models/Profile'])) {
    require_once 'src/core/models/'.substr($class, 12).'.php';
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


if (is_array(Config::config('trusted_domains')) &&
    isset($_SERVER['HTTP_HOST']) &&
    !in_array($_SERVER['HTTP_HOST'], Config::config('trusted_domains'))) {
  die($_SERVER['HTTP_HOST'].' is not a trusted domain. It can be added in configuration file.');
}

$db = new Db(Config::config('db'));

if ($GLOBALS['config']['env'] == 'dev') {
  error_reporting(E_ALL);
  ini_set('display_errors', '1');
  ini_set('display_startup_errors', '1');
  Config::load();
} else {
  error_reporting(E_ERROR);
  ini_set('display_errors', 0);
  ini_set('display_startup_errors', 0);
  if (!include LOG_PATH.'/load.php') {
    Config::load();
    Package::updateLoadFile();
  }
}

Event::fire('load');

function __($key, $alt = null)
{
  return Config::tr($key, $alt);
}

$theme = Router::request('g_preview_theme', $GLOBALS['config']['theme']);
if (file_exists("themes/$theme/load.php")) {
  include "themes/$theme/load.php";
}
if (is_array(Config::config('cors'))) {
  foreach (Config::config('cors') as $url) {
    @header('Access-Control-Allow-Origin: '.$url);
  }
}

Router::run($_GET['url'] ?? '');
