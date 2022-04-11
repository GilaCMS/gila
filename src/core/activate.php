<?php

define('FS_ACCESS', true);
define('LOG_PATH', 'log');
$package = $argv[1] ?? 'core';

$classMap = include __DIR__.'/classmap.php';

spl_autoload_register(function ($class) {
  global $classMap;
  if (isset($classMap[$class])) {
    require_once $classMap[$class];
    return true;
  }
});

if (file_exists('vendor/autoload.php')) {
  include_once 'vendor/autoload.php';
}

include 'config.php';
Gila\DB::set($GLOBALS['config']['db']);
Gila\Config::loadOptions();
if (file_exists('.env')) {
  Gila\Config::loadEnv();
}

Gila\Package::activate($package);
