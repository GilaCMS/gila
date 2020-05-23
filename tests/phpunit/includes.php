<?php

chdir(__DIR__.'/../../');
include_once(__DIR__.'/../../vendor/autoload.php');
include_once(__DIR__.'/../../src/core/classes/Gila.php');
include_once(__DIR__.'/../../src/core/classes/Router.php');
include_once(__DIR__.'/../../src/core/classes/FileManager.php');
include_once(__DIR__.'/../../src/core/classes/Db.php');
include_once(__DIR__.'/../../src/core/classes/Session.php');
include_once(__DIR__.'/../../src/core/classes/View.php');
include_once(__DIR__.'/../../src/core/classes/Event.php');
define('SITE_PATH', '');
define('LOG_PATH', 'log');
define('CONFIG_PHP', 'config.php');
define('FS_ACCESS', true);

$GLOBALS['user_privileges'] = ['admin'];
$db = new Db("127.0.0.1", "g_user", "password", "g_db");
$db = new Db("127.0.0.1", "guser", "12345", "gila150");
