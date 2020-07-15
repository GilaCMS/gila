<?php

use Gila\Config; 
use Gila\Router; 
use Gila\Event; 
use Gila\Db; 
use Gila\Session; 

$site_folder = 'sites/'.$_SERVER['HTTP_HOST'];
if (file_exists($site_folder)) {
  define('SITE_PATH', $site_folder.'/');
  define('LOG_PATH', $site_folder.'/log');
  define('CONFIG_PHP', $site_folder.'/config.php');
  define('FS_ACCESS', false);
} else {
  define('SITE_PATH', '');
  define('LOG_PATH', 'log');
  define('CONFIG_PHP', 'config.php');
  define('FS_ACCESS', true);
}

$starttime = microtime(true);

if (!isset($_GET['url'])) {
  $_GET['url'] = substr($_SERVER['REQUEST_URI'], 1);
}

ini_set("error_log", "log/error.log");

spl_autoload_register(function ($class) {
  global $classMap;
  $classMap = [
    'Gila\\Cache'=> 'src/core/classes/Cache.php',
    'Gila\\Controller'=> 'src/core/classes/Controller.php',
    'Gila\\Db'=> 'src/core/classes/Db.php',
    'Gila\\Event'=> 'src/core/classes/Event.php',
    'Gila\\FileManager'=> 'src/core/classes/FileManager.php',
    'Gila'=> 'src/core/classes/Config.php',
    'gForm'=> 'src/core/classes/Form.php',
    'gTable'=> 'src/core/classes/Table.php',
    'Gila\\Form'=> 'src/core/classes/Form.php',
    'Gila\\Table'=> 'src/core/classes/Table.php',
    'Gila\\TableSchema'=> 'src/core/classes/TableSchema.php',
    'Gila\\Image'=> 'src/core/classes/Image.php',
    'Gila\\Logger'=> 'src/core/classes/Logger.php',
    'Gila\\Menu'=> 'src/core/classes/Menu.php',
    'Gila\\Package'=> 'src/core/classes/Package.php',
    'Gila\\Router'=> 'src/core/classes/Router.php',
    'Gila\\Session'=> 'src/core/classes/Session.php',
    'Gila\\Slugify'=> 'src/core/classes/Slugify.php',
    'Gila\\Theme'=> 'src/core/classes/Theme.php',
    'Gila\\View'=> 'src/core/classes/View.php',
    'gpost'=> 'src/core/classes/HttpPost.php',
    'Gila\\HttpPost'=> 'src/core/classes/HttpPost.php',
    'Gila\\HtmlInput'=> 'src/core/classes/HtmlInput.php',
    'Gila\\User'=> 'src/core/models/User.php',
    'Gila\\Widget'=> 'src/core/models/Widget.php',
    'Gila\\Page'=> 'src/core/models/Page.php',
    'Gila\\Profile'=> 'src/core/models/Profile.php',
    'Gila\\Post'=> 'src/core/models/Post.php',
    'Gila\\Config'=> 'src/core/classes/Config.php',
  ];

  if (isset($classMap[$class])) {
    require_once $classMap[$class];
    return true;
  }

  $class = strtr($class, ['\\'=>'/', '__'=>'-']);
  $Class = ucfirst($class);

  if (file_exists('src/core/classes/'.$class.'.php')) {
    require_once 'src/core/classes/'.$class.'.php';
    class_alias('Gila\\'.$class, $class);
  } elseif (file_exists('src/core/classes/'.$Class.'.php')) {
    trigger_error("Class name $Class is capitalized", E_USER_WARNING);
    require_once 'src/core/classes/'.$Class.'.php';
  } elseif (file_exists('src/'.$class.'.php')) {
    require_once 'src/'.$class.'.php';
    if(in_array($class, ['core/models/Post', 'core/models/Page', 'core/models/Widget', 'core/models/User', 'core/models/Profile'])) {
      class_alias('Gila\\'.substr($class,12), strtr($class, ['/'=>'\\']));
    }
  }
});
if (file_exists('vendor/autoload.php')) {
  $loader = include 'vendor/autoload.php';
}

$GLOBALS['config'] = [];
@require_once CONFIG_PHP;
if($GLOBALS['config'] === []) {
  if (isset($_GET['install'])) {
    include 'src/core/install/index.php';
  } else {
    echo "Gila CMS is not installed.<meta http-equiv=\"refresh\" content=\"2;url=".Config::base_url()."?install\" />";
  }
  exit;
}


$GLOBALS['lang'] = [];
function __($key, $alt = null) {
  return Config::tr($key, $alt);
}

function _url($url) {
  return str_replace(['\'','"','<','>',':'], ['%27','%22','%3C','%3E','%3A'], $url);
}


if (is_array(Config::config('trusted_domains')) &&
    isset($_SERVER['HTTP_HOST']) &&
    !in_array($_SERVER['HTTP_HOST'], Config::config('trusted_domains'))) {
  die($_SERVER['HTTP_HOST'].' is not a trusted domain. It can be added in configuration file.');
}

$db = new Db(Config::config('db'));

if ($GLOBALS['config']['env'] == 'dev') {
  error_reporting(E_ALL);
  ini_set('display_errors', '1');
  ini_set('display_startup_errors', '1');
  Config::load();
} else {
  error_reporting(E_ERROR);
  ini_set('display_errors', 0);
  ini_set('display_startup_errors', 0);
  if (!include LOG_PATH.'/load.php') {
    Config::load();
    Package::updateLoadFile();
  }
}

Event::fire('load');

$theme = Router::request('g_preview_theme', $GLOBALS['config']['theme']);
if (file_exists("themes/$theme/load.php")) {
  include "themes/$theme/load.php";
}
if (is_array(Config::config('cors'))) {
  foreach (Config::config('cors') as $url) {
    @header('Access-Control-Allow-Origin: '.$url);
  }
}

Router::run($_GET['url'] ?? '');
