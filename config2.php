<?php

$GLOBALS = array (
  'title' => 'Gila CMS',
  'slogan' => 'An awesome website!',
  'base' => '//192.168.1.69/gila/',
  'db' => 
  array (
    'host' => 'localhost',
    'user' => 'myuser',
    'pass' => '1234',
    'name' => 'gila',
  ),
  'default-controller' => 'blog',
  'version' => '1.0',
  'theme' => 'newsfeed',
  'controller' => 
  array (
    'admin' => 'core/controllers/admin',
    'blog' => 'core/controllers/blog',
  ),
  'packages' => 
  array (
    0 => 'classifieds',
  ),
  'path' => 
  array (
    'controller' => 
    array (
      'cls' => 'classifieds/controllers/cls',
    ),
  ),
  'timezone' => 'Africa/Addis_Ababa',
)