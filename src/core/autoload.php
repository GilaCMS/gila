<?php

spl_autoload_register(function ($class) {
  global $classMap;
  if (isset($classMap[$class])) {
    require_once $classMap[$class];
    return true;
  }
  $class = strtr($class, ['\\'=>'/', '__'=>'-']);
  if (file_exists('src/core/classes/'.$class.'.php')) {
    require_once 'src/core/classes/'.$class.'.php';
    class_alias('Gila\\'.$class, $class);
    return true;
  } elseif (file_exists('src/'.$class.'.php')) {
    require_once 'src/'.$class.'.php';
    return true;
  }
});


$classMap = [
  'Gila\\Cache'=> 'src/core/classes/Cache.php',
  'Gila\\Controller'=> 'src/core/classes/Controller.php',
  'Gila\\DbClass'=> 'src/core/classes/DbClass.php',
  'Gila\\DB'=> 'src/core/classes/DB.php',
  'Gila\\Email'=> 'src/core/classes/Email.php',
  'Gila\\Event'=> 'src/core/classes/Event.php',
  'Gila\\FileManager'=> 'src/core/classes/FileManager.php',
  'Gila\\Form'=> 'src/core/classes/Form.php',
  'Gila\\Table'=> 'src/core/classes/Table.php',
  'Gila\\TableSchema'=> 'src/core/classes/TableSchema.php',
  'Gila\\Image'=> 'src/core/classes/Image.php',
  'Gila\\Logger'=> 'src/core/classes/Logger.php',
  'Gila\\Log'=> 'src/core/classes/Log.php',
  'Gila\\Menu'=> 'src/core/classes/Menu.php',
  'Gila\\MenuItemTypes'=> 'src/core/classes/MenuItemTypes.php',
  'Gila\\Package'=> 'src/core/classes/Package.php',
  'Gila\\Request'=> 'src/core/classes/Request.php',
  'Gila\\Response'=> 'src/core/classes/Response.php',
  'Gila\\Router'=> 'src/core/classes/Router.php',
  'Gila\\Session'=> 'src/core/classes/Session.php',
  'Gila\\Slugify'=> 'src/core/classes/Slugify.php',
  'Gila\\Sendmail'=> 'src/core/classes/Sendmail.php',
  'Gila\\Theme'=> 'src/core/classes/Theme.php',
  'Gila\\View'=> 'src/core/classes/View.php',
  'Gila\\HttpPost'=> 'src/core/classes/HttpPost.php',
  'Gila\\HtmlInput'=> 'src/core/classes/HtmlInput.php',
  'Gila\\User'=> 'src/core/classes/User.php',
  'Gila\\Widget'=> 'src/core/classes/Widget.php',
  'Gila\\Page'=> 'src/core/classes/Page.php',
  'Gila\\Profile'=> 'src/core/classes/Profile.php',
  'Gila\\Post'=> 'src/core/classes/Post.php',
  'Gila\\Config'=> 'src/core/classes/Config.php',
  'Gila\\UserAgent'=> 'src/core/classes/UserAgent.php',
  'Gila\\UserNotification'=> 'src/core/classes/UserNotification.php',
];

if (file_exists('vendor/autoload.php')) {
  include_once 'vendor/autoload.php';
}
