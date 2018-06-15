<?php

global $db;
$GLOBALS['version']='1.7.2';
gila::controllers([
    'admin'=> 'core/controllers/admin',
    'api'=> 'core/controllers/api',
    'blog'=> 'core/controllers/blog',
    'fm'=> 'core/controllers/fm'
]);
gila::$amenu = [
    ['Dashboard','admin','icon'=>'dashboard'],
    'content'=>['Content','#','icon'=>'newspaper-o','access'=>'editor admin','children'=>[
        ['Pages','admin/content/page','icon'=>'file','access'=>'admin'],
        ['Posts','admin/content/post','icon'=>'pencil','access'=>'admin writer'],
        ['Categories','admin/content/postcategory','icon'=>'bars','access'=>'admin'],
        ['Media','admin/media','icon'=>'image','access'=>'admin'],
        ['File Manager','fm','icon'=>'folder','access'=>'admin'],
        ['BD Backups','admin/db_backup','icon'=>'database','access'=>'admin'],
        ]],
    'admin'=>['Administration','#','icon'=>'wrench','access'=>'admin','children'=>[
        ['Users','admin/content/user','icon'=>'users','access'=>'admin'],
        ['Main Menu','admin/menu','icon'=>'bars','access'=>'admin'],
        ['Widgets','admin/widgets','icon'=>'th-large','access'=>'admin'],
        ['Packages','admin/packages','icon'=>'dropbox','access'=>'admin'],
        ['Themes','admin/themes','icon'=>'paint-brush','access'=>'admin'],
        ['Settings','admin/settings','icon'=>'cogs','access'=>'admin'],
        ['PHPinfo','admin/phpinfo','icon'=>'info-circle','access'=>'admin'],
        ]],
];

gila::widgets([
  'text'=>'core/widgets/text',
  'latest-post'=>'core/widgets/latest-post',
  'category-post'=>'core/widgets/category-post',
  'social-icons'=>'core/widgets/social-icons',
  'tag'=>'core/widgets/tag',
  'basic'=>'core/widgets/basic',
  'links'=>'core/widgets/links',
  'features'=>'core/widgets/features',
  //'contact-form'=>'core/widgets/contact-form'
]);
gila::$widget_area=[];

gila::$option=[];
$res = $db->get('SELECT `option`,`value` FROM `option`;');
foreach($res as $r) gila::$option[$r[0]] = $r[1];

gila::$privilege['admin']="Administrator access.";
gila::$privilege['editor']="Can publish or edit posts from other users.";
gila::$privilege['developer']="Special access in developer tools.";

gila::content('post','core/tables/post.php');
gila::content('postcategory','core/tables/postcategory.php');
gila::content('user','core/tables/user.php');
gila::content('page','core/tables/page.php');
gila::content('widget','core/tables/widget.php');

/*foreach(gila::$content as $key=>$path) {
    gila::$amenu['content']['children'][$key] = [ucfirst($key), 'admin/content/'.$key, 'access'=>'admin'];
}*/

gila::addLang('core/lang/');

if(gila::config('use_cdn')==1)
    include_once 'src/core/assets/cdn_paths.php';
