<?php

use Gila\Config;
use Gila\Router;
use Gila\View;

global $db;

Router::controller('admin', 'core/controllers/AdminController');
Router::controller('cm', 'core/controllers/CMController');
Router::controller('user', 'core/controllers/UserController');
Router::controller('webhook', 'core/controllers/WebhookController');
Router::controller('lzld', 'core/controllers/LZLDController');
Router::controller('fm', 'core/controllers/FMController');
Router::controller('blocks', 'core/controllers/BlocksController');

Config::$amenu = [
  ['Dashboard','admin','icon'=>'dashboard'],
  'content'=>['Content','#','icon'=>'newspaper-o','access'=>'admin editor','children'=>[
    ['Pages','admin/content/page','icon'=>'file','access'=>'admin editor'],
    ['Posts','admin/content/post','icon'=>'pencil','access'=>'admin editor'],
    ['Categories','admin/content/postcategory','icon'=>'bars','access'=>'admin editor'],
    ['Media','admin/media','icon'=>'image','access'=>'admin editor'],
  ]],
  'admin'=>['Administration','#','icon'=>'wrench','access'=>'admin editor','children'=>[
    ['Users','admin/users','icon'=>'users','access'=>'admin admin_user admin_userrole admin_permissions'],
    ['Main Menu','admin/menu','icon'=>'bars','access'=>'admin editor'],
    ['Widgets','admin/content/widget','icon'=>'th-large','access'=>'admin editor'],
    ['Packages','admin/packages','icon'=>'dropbox','access'=>'admin'],
    ['Themes','admin/themes','icon'=>'paint-brush','access'=>'admin'],
    ['Settings','admin/settings','icon'=>'cogs','access'=>'admin']
  ]],
  ['Posts','admin/content/user-post','icon'=>'pencil','access'=>'writer'],
];

if (FS_ACCESS) {
  Config::amenu_child('admin', ['Logs','admin/fm?f=log','icon'=>'folder','access'=>'admin']);
}

Config::widgets([
  'paragraph'=>'core/widgets/paragraph', // DEPRECATED
  'text'=>'core/widgets/text',
  'image'=>'core/widgets/image',
  'side-image'=>'core/widgets/side-image',
  'features'=>'core/widgets/features',
  'latest-post'=>'core/widgets/latest-post',
  'category-post'=>'core/widgets/category-post',
  'post-categories'=>'core/widgets/post-categories',
  'gallery'=>'core/widgets/gallery',
  'gallery-links'=>'core/widgets/gallery-links',
  'social-icons'=>'core/widgets/social-icons',
  'links'=>'core/widgets/links', // DEPRECATED
  'contact-form'=>'core/widgets/contact-form',
  'core-counters'=>'core/widgets/core-counters',
  'search'=>'core/widgets/search',
  'tag'=>'core/widgets/tag',
  'html'=>'core/widgets/html',
]);
Config::$widget_area=['dashboard'];

Config::content('post', 'core/tables/post.php');
Config::content('user-post', 'core/tables/user-post.php');
Config::content('postcategory', 'core/tables/postcategory.php');
Config::content('user', 'core/tables/user.php');
Config::content('userrole', 'core/tables/userrole.php');
Config::content('page', 'core/tables/page.php');
Config::content('widget', 'core/tables/widget.php');

Config::addLang('core/lang/');

if (Config::get('use_cdn')==='1') {
  View::$cdn_paths = include 'src/core/cdn_paths.php';
}
