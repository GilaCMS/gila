<?php

chdir(__DIR__.'/../../');
include_once __DIR__.'/../../vendor/autoload.php';
include_once __DIR__.'/../../src/core/classes/Config.php';
include_once __DIR__.'/../../src/core/classes/Router.php';
include_once __DIR__.'/../../src/core/classes/FileManager.php';
include_once __DIR__.'/../../src/core/classes/Db.php';
include_once __DIR__.'/../../src/core/classes/Session.php';
include_once __DIR__.'/../../src/core/classes/View.php';
include_once __DIR__.'/../../src/core/classes/Event.php';
define('SITE_PATH', '');
define('LOG_PATH', 'log');
define('CONFIG_PHP', 'config.php');
define('FS_ACCESS', true);

$GLOBALS['user_privileges'] = ['admin'];
$db = new Gila\Db("127.0.0.1", "g_user", "password", "g_db");

$GLOBALS['lang'] = [];
function __($key, $alt = null) {
  return Gila\Config::tr($key, $alt);
}

class_alias('Gila\\Config', 'Config');
class_alias('Gila\\View', 'View');
