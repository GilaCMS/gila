<?php

global $db;

Router::controller('admin', 'core/controllers/admin');
Router::controller('cm', 'core/controllers/cm');
Router::controller('login', 'core/controllers/login');
Router::controller('webhook', 'core/controllers/webhook');
Router::controller('lzld', 'core/controllers/lzld');
Router::controller('fm', 'core/controllers/fm');

Gila::$amenu = [
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
  Gila::amenu_child('admin', ['Logs','admin/fm?f=log','icon'=>'folder','access'=>'admin']);
}

Gila::widgets([
  'paragraph'=>'core/widgets/paragraph',
  'image'=>'core/widgets/image',
  'gallery'=>'core/widgets/gallery',
  'gallery-links'=>'core/widgets/gallery-links',
  'social-icons'=>'core/widgets/social-icons',
  'links'=>'core/widgets/links',
  'features'=>'core/widgets/features',
  'latest-post'=>'core/widgets/latest-post',
  'category-post'=>'core/widgets/category-post',
  'post-categories'=>'core/widgets/post-categories',
  'tag'=>'core/widgets/tag',
  'contact-form'=>'core/widgets/contact-form'
]);
Gila::$widget_area=['dashboard'];

Gila::$privilege['admin']="Administrator access.";
Gila::$privilege['editor']="Can publish or edit posts from other users.";
Gila::$privilege['developer']="Special access in developer tools.";

Gila::content('post', 'core/tables/post.php');
Gila::content('user-post', 'core/tables/user-post.php');
Gila::content('postcategory', 'core/tables/postcategory.php');
Gila::content('user', 'core/tables/user.php');
Gila::content('userrole', 'core/tables/userrole.php');
Gila::content('page', 'core/tables/page.php');
Gila::content('widget', 'core/tables/widget.php');

Gila::addLang('core/lang/');

if (Gila::config('use_cdn')=='1') {
  View::$cdn_paths = include('src/core/cdn_paths.php');
}
