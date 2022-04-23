<?php

use Gila\Config;
use Gila\Router;
use Gila\Event;
use Gila\DbClass;
use Gila\DB;
use Gila\Session;
use Gila\Logger;

$site_folder = 'sites/'.($_SERVER['HTTP_HOST']??'');
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
include_once __DIR__.'/autoload.php';

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

$db = new DbClass($GLOBALS['config']['db']);
DB::set($GLOBALS['config']['db']);

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
