<?php

define('FS_ACCESS', true);
define('LOG_PATH', 'log');
$package = $argv[1] ?? 'core';

include __DIR__.'/autoload.php';

include 'config.php';
Gila\DB::set($GLOBALS['config']['db']);
Gila\Config::loadOptions();
if (file_exists('.env')) {
  Gila\Config::loadEnv();
}

Gila\Package::activate($package);
