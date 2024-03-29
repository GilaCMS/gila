<?php

chdir(__DIR__.'/../../');
include_once __DIR__.'/../../vendor/autoload.php';
include_once __DIR__.'/../../src/core/classes/Config.php';
include_once __DIR__.'/../../src/core/classes/Router.php';
include_once __DIR__.'/../../src/core/classes/FileManager.php';
include_once __DIR__.'/../../src/core/classes/DbClass.php';
include_once __DIR__.'/../../src/core/classes/Session.php';
include_once __DIR__.'/../../src/core/classes/View.php';
include_once __DIR__.'/../../src/core/classes/Event.php';
include_once __DIR__.'/../../src/core/classes/DB.php';
define('SITE_PATH', '');
define('LOG_PATH', 'log');
define('TMP_PATH', 'tmp');
define('CONFIG_PHP', 'config.php');
define('FS_ACCESS', true);

Gila\Config::dir(LOG_PATH.'/cacheItem/');
Gila\Session::$data['permissions'] = ['admin'];
$db = new Gila\DbClass("127.0.0.1", "g_user", "password", "g_db");
Gila\DB::set(['host'=>"127.0.0.1", 'user'=>"g_user", 'pass'=>"password", 'name'=>"g_db"]);

$GLOBALS['lang'] = [];
function __($key, $alt = null)
{
  return Gila\Config::tr($key, $alt);
}

class_alias('Gila\\Config', 'Config');
class_alias('Gila\\View', 'View');
