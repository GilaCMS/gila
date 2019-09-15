<?php

global $db;

$GLOBALS['version']='1.11.2';
gila::controllers([
  'admin'=> 'core/controllers/admin',
  'api'=> 'core/controllers/api',
  'cm'=> 'core/controllers/cm',
  'login'=> 'core/controllers/login',
  'webhook'=> 'core/controllers/webhook',
  'lzld'=> 'core/controllers/lzld'
]);

gila::$amenu = [
  ['Dashboard','admin','icon'=>'dashboard'],
  'content'=>['Content','#','icon'=>'newspaper-o','access'=>'admin editor','children'=>[
    ['Pages','admin/content/page','icon'=>'file','access'=>'admin'],
    ['Posts','admin/content/post','icon'=>'pencil','access'=>'admin editor'],
    ['Categories','admin/content/postcategory','icon'=>'bars','access'=>'admin editor'],
    ['Media','admin/media','icon'=>'image','access'=>'admin editor'],
    ['BD Backups','admin/db_backup','icon'=>'database','access'=>'admin'],
  ]],
  ['Posts','admin/content/user-post','icon'=>'pencil','access'=>'writer'],
  'admin'=>['Administration','#','icon'=>'wrench','access'=>'admin','children'=>[
    ['Users','admin/users','icon'=>'users','access'=>'admin'],
    ['Main Menu','admin/menu','icon'=>'bars','access'=>'admin'],
    ['Widgets','admin/content/widget','icon'=>'th-large','access'=>'admin'],
    ['Packages','admin/packages','icon'=>'dropbox','access'=>'admin'],
    ['Themes','admin/themes','icon'=>'paint-brush','access'=>'admin'],
    ['Settings','admin/settings','icon'=>'cogs','access'=>'admin']
  ]],
];

if(FS_ACCESS) {
  gila::controller('fm', 'core/controllers/fm');
  gila::amenu_child('content', ['File Manager','admin/fm','icon'=>'folder','access'=>'admin']);
  gila::amenu_child('admin', ['PHPinfo','admin/phpinfo','icon'=>'info-circle','access'=>'admin']);
}

gila::widgets([
  'paragraph'=>'core/widgets/paragraph',
  'image'=>'core/widgets/image',
  'gallery'=>'core/widgets/gallery',
  'social-icons'=>'core/widgets/social-icons',
  'links'=>'core/widgets/links',
  'features'=>'core/widgets/features',
  'latest-post'=>'core/widgets/latest-post',
  'category-post'=>'core/widgets/category-post',
  'post-categories'=>'core/widgets/post-categories',
  'tag'=>'core/widgets/tag',
  'contact-form'=>'core/widgets/contact-form'
]);
gila::$widget_area=['dashboard'];

gila::$privilege['admin']="Administrator access.";
gila::$privilege['editor']="Can publish or edit posts from other users.";
gila::$privilege['developer']="Special access in developer tools.";

gila::content('post','core/tables/post.php');
gila::content('user-post','core/tables/user-post.php');
gila::content('postcategory','core/tables/postcategory.php');
gila::content('user','core/tables/user.php');
gila::content('userrole','core/tables/userrole.php');
gila::content('page','core/tables/page.php');
gila::content('widget','core/tables/widget.php');

/*foreach(gila::$content as $key=>$path) {
    gila::$amenu['content']['children'][$key] = [ucfirst($key), 'admin/content/'.$key, 'access'=>'admin'];
}*/

gila::addLang('core/lang/');

if(gila::config('use_cdn')==1) {
  include_once 'src/core/assets/cdn_paths.php';
}
