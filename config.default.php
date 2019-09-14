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
  'use_webp' => '0', # deliver webp photos for browsers that support it
  'base' => 'http://localhost/gila/', # http://yourwebsite.com/
  'theme' => 'gila-blog',
  'title' => 'Gila CMS',
  'slogan' => 'An awesome website!',
  'default-controller' => 'blog',
  'timezone' => 'America/Mexico_City',
  'env' => 'dev',
  'language' => 'en',
  'rewrite' => '1',
  'default.menu' => '0',
  'user_register' => '0',
  'use_cdn' => '0',
  'admin_email' => 'admin@mail.com',
  'admin_logo' => '',
  'check4updates' => '1'
);
