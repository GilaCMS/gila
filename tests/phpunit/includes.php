<?php

chdir(__DIR__.'/../../');
include __DIR__.'/../../vendor/autoload.php';
include __DIR__.'/../../src/core/classes/Gila.php';
include __DIR__.'/../../src/core/classes/Router.php';
include __DIR__.'/../../src/core/classes/FileManager.php';
include __DIR__.'/../../src/core/classes/Db.php';
define('SITE_PATH', '');
define('LOG_PATH', 'log');
define('CONFIG_PHP', 'config.php');
define('FS_ACCESS', true);

use PHPUnit\Framework\TestCase;
$GLOBALS['user_privileges'] = ['admin'];
$db = new Db("127.0.0.1", "g_user", "password", "g_db");
