<?php

global $db;
$GLOBALS['version']='1.5.0';
gila::controllers([
    'admin'=> 'core/controllers/admin',
    'api'=> 'core/controllers/api',
    'blog'=> 'core/controllers/blog',
    'fm'=> 'core/controllers/fm'
]);
gila::$amenu = [
    ['Dashboard','admin','icon'=>'dashboard'],
    'content'=>['Content','admin','icon'=>'newspaper-o','access'=>'editor admin','children'=>[
        ['Pages','admin/pages','icon'=>'file','access'=>'admin'],
        ['Posts','admin/posts','icon'=>'pencil','access'=>'admin writer'],
        ['Categories','admin/postcategories','icon'=>'bars','access'=>'admin'],
        ['Media','admin/media','icon'=>'image','access'=>'admin'],
        ['BD Backups','admin/db_backup','icon'=>'database','access'=>'admin'],
        ]],
    'admin'=>['Administration','admin','icon'=>'wrench','access'=>'admin','children'=>[
        ['Users','admin/users','icon'=>'users','access'=>'admin'],
        ['Main Menu','admin/menu','icon'=>'bars','access'=>'admin'],
        ['Widgets','admin/widgets','icon'=>'th-large','access'=>'admin'],
        ['Packages','admin/addons','icon'=>'dropbox','access'=>'admin'],
        ['Themes','admin/themes','icon'=>'paint-brush','access'=>'admin'],
        ['Settings','admin/settings','icon'=>'cogs','access'=>'admin'],
        ['File Manager','fm','icon'=>'folder','access'=>'admin'],
        ['PHPinfo','admin/phpinfo','icon'=>'info-circle','access'=>'admin'],
        ]],
];

gila::widgets([
  'menu'=>'core/widgets/menu',
  'text'=>'core/widgets/text',
  'latest-post'=>'core/widgets/latest-post',
  'social-icons'=>'core/widgets/social-icons',
  'tag'=>'core/widgets/tag'
]);
gila::$widget_area=[];

gila::$option=[];
$res = $db->get('SELECT `option`,`value` FROM `option`;');
foreach($res as $r) gila::$option[$r[0]] = $r[1];

gila::$privilege['admin']="Administrator access.";
gila::$privilege['editor']="Can publish or edit posts from other users.";
gila::$privilege['developer']="Special access in developer tools.";

gila::content('post','core/tables/post.php');
gila::content('user','core/tables/user.php');
gila::content('page','core/tables/page.php');
gila::content('widget','core/tables/widget.php');
gila::addLang('core/lang/');
