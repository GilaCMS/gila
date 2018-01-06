<?php

$GLOBALS['config'] = array (
  'db' =>
  array (
    'host' => 'localhost', # Database hostname, usually is localhost
    'user' => 'root', # The database user
    'pass' => '', # The database user's password
    'name' => 'gila', # The database name
  ),
  'packages' =>
  array (
  ),
  'base' => 'http://localhost/gila/', # http://yourwebsite.com/
  'theme' => 'gila-blog',
  'title' => 'Gila CMS',
  'slogan' => 'An awesome website!',
  'default-controller' => 'blog',
  'timezone' => 'America/Mexico_City',
  'env' => 'dev',
  'rewrite' => '1',
  'default.menu' => '0',
  'user_register' => '0'
);
