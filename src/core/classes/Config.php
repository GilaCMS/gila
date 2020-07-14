<?php
namespace Gila;

class Config
{
  public static $widget;
  public static $package;
  public static $amenu;
  public static $widget_area = [];
  public static $option;
  public static $privilege;
  public static $content;
  public static $contentField;
  public static $contentInit = [];
  public static $mt;
  public static $base_url;
  public static $langPaths = [];
  public static $langLoaded = false;

  /**
  * Registers new a controller
  * @param $c (string) Controller name as given in url path
  * @param $file (string) Controller's filepath without the php extension
  * @param $name (string) Controller's class name, $c is used by default
  * @code
  * Config::controller('my-ctrl', 'my_package/controllers/ctrl','myctrl');
  * @endcode
  */
  public static function controller($c, $path, $name=null) // DEPRECATED
  {
    Router::controller($c, $path);
  }

  /**
  * Registers a function call on a specific path
  * @param $r (string) The path
  * @param $fn (function) Callback for the route
  * @code
  * Config::route('some.txt', function(){ echo 'Some text.'; });
  * @endcode
  */
  public static function route($r, $fn) // DEPRECATED
  {
    Router::add($r, $fn);
  }

  /**
  * Registers a function to run right after the controller class construction
  * @param $c (string) The controller's class name
  * @param $fn (function) Callback
  * @code
  * Config::onController('blog', function(){ BlogController::ppp = 24; });
  * @endcode
  */
  public static function onController($c, $fn) // DEPRECATED
  {
    Router::$on_controller[$c][] = $fn;
  }

  /**
  * Registers a new action or replaces an existing for a controller
  * @param $c (string) The controller
  * @param $action (string) The action
  * @param $fn (function) Callback
  * @code
  * Config::action('blog', 'topics', function(){ ... });
  * @endcode
  */
  public static function action($c, $action, $fn) // DEPRECATED -> Router::action()
  {
    Router::action($c, $action, $fn);
  }

  /**
  * Registers a function to run before the function of a specific action
  * @param $c (string) The controller
  * @param $action (string) The action
  * @param $fn (function) Callback
  */
  public static function before($c, $action, $fn) // DEPRECATED -> Router::before()
  {
    Router::before($c, $action, $fn);
  }

  public static function onAction($c, $action, $fn) // DEPRECATED -> Router::onAction()
  {
    Router::onAction($c, $action, $fn);
  }

  /**
  * Adds language translations from a json file
  * @param $path (string) Path to the folder/prefix of language json files
  */
  public static function addLang($path)
  {
    if (in_array($path, self::$langPaths)) {
      return;
    }

    if (self::$langLoaded===true) {
      self::loadLang($path);
    }
    self::$langPaths[] = $path;
  }

  public static function loadLang($path)
  {
    $filepath = 'src/'.$path.self::config('language').'.json';
    if (file_exists($filepath)) {
      $GLOBALS['lang'] = @array_merge(
        @$GLOBALS['lang'],
        json_decode(file_get_contents($filepath), true)
      );
    }
  }

  public static function addList($list, $el)
  {
    @$GLOBALS['list'][$list][] = $el;
  }

  public static function getList($list)
  {
    return $GLOBALS['list'][$list] ?? [];
  }

  /**
  * Registers new widgets
  * @param $list (Assoc Array) Widgets to register
  * @code Config::widgets( [‘wdg’=>’my_package/widgets/wdg’] ); @endcode
  */
  public static function widgets($list)
  {
    foreach ($list as $k=>$item) {
      self::$widget[$k]=$item;
    }
  }

  /**
  * Registers new content type
  * @param $key (string) Name of content type
  * @param $path (string) Path to the table file
  * @code Config::content( 'mytable', 'package_name/content/mytable.php' ); @endcode
  */
  public static function content($key, $path)
  {
    self::$content[$key] = $path;
  }

  /**
  * Make changes of a field of content type
  * @param $key (string) Name of content type
  * @param $field (string) Index of the field
  * @param $table (Assoc array) Value of the field
  * DEPRECATED @see Config::contentInit()
  */
  public static function contentField($key, $field, $table)
  {
    if (!isset(self::$contentField[$key])) {
      self::$contentField[$key] = [];
    }
    self::$contentField[$key][$field] = $table;
  }

  /**
  * Make changes in a content type when it is initialized
  * @param $key (string) Name of content type
  * @param $init (function) Funtion to run
  * @code Config::contentInt( 'mytable', function(&$table) { $table['fileds']['new_field']=[];} ); @endcode
  */
  public static function contentInit($key, $init)
  {
    @self::$contentInit[$key][] = $init;
    if (isset(Table::$tableList[$key])) {
      unset(Table::$tableList[$key]);
    }
  }

  /**
  * Returns the list of active packages
  * @return Array
  */
  public static function packages()
  {
    return $GLOBALS['config']['packages'];
  }

  /**
  * Add new elements on administration menu
  * @param $key (string) Index name
  * @param $item (assoc array) Array with data
  * Indices 0 for Display name, 1 for action link
  * @code Config::amenu('item', ['Item','controller/action','icon'=>'item-icon']); @endcode
  */
  public static function amenu($key, $item=[])
  {
    if (!is_array($key)) {
      $list = [];
      $list[$key] = $item;
    } else {
      $list = $key;
    }
    foreach ($list as $k=>$i) {
      if (is_numeric($k)) {
        self::$amenu[]=$i; // DEPRECATED
      } else {
        self::$amenu[$k]=$i;
      }
    }
  }

  /**
  * Add a child element on administration menu item
  * @param $key (string) Index of parent item
  * @param $item (assoc array) Array with data
  * @code Config::amenu_child('item', ['Child Item','controller/action','icon'=>'item-icon']); @endcode
  */
  public static function amenu_child($key, $item)
  {
    if (!isset(self::$amenu[$key]['children'])) {
      self::$amenu[$key]['children']=[];
    }
    self::$amenu[$key]['children'][]=$item;
  }

  /**
  * Sets or gets the value of configuration attribute
  * @param $key (string) Name of the attribute
  * @param $value (optional) The value to set @see setConfig()
  * @return The value if parameter $value is not sent
  */
  public static function config($key, $value = null)
  {
    if ($value === null) { // DEPRECATED should use setConfig()
      if (isset($GLOBALS['config'][$key])) {
        return $GLOBALS['config'][$key];
      } else {
        return null;
      }
    } else {
      $GLOBALS['config'][$key] = $value;
    }
  }

  /**
  * Sets the value of configuration attribute
  * @param $key (string) Name of the attribute
  * @param $value (optional) The value to set
  */
  public static function setConfig($key, $value)
  {
    if (!is_string($key)) {
      return;
    }
    $GLOBALS['config'][$key] = $value;
  }

  /**
  * Rewrites the config.php file
  */
  public static function updateConfigFile()
  {
    $filedata = "<?php\n\n\$GLOBALS['config'] = ".var_export($GLOBALS['config'], true).";";
    file_put_contents(CONFIG_PHP, $filedata);
  }

  /**
  * DEPRECATED @see View::alert()
  */
  public static function alert($type, $msg)
  {
    if ($type === 'alert') {
      $type = '';
    }
    return "<div class='alert $type'><span class='closebtn' onclick='this.parentElement.style.display=\"none\";'>&times;</span>$msg</div>";
  }

  /**
  * @return Password hash
  */
  public static function hash($pass)
  {
    return password_hash($pass, PASSWORD_BCRYPT);
  }

  /**
  * Returns an option value
  * @param $option (string) Option name
  * @param $default (optional) The value to return if this option has not saved value
  * @return The option value
  */
  public static function option($option, $default='')
  {
    if (isset(self::$option[$option]) && self::$option[$option]!='') {
      return self::$option[$option];
    }
    return $default;
  }

  /**
  * Sets an option value
  * @param $option (string) Option name
  * @param $value (optional) The value to set
  */
  public static function setOption($option, $value='')
  {
    global $db;
    @self::$option[$option] = $value;
    $ql="INSERT INTO `option`(`option`,`value`) VALUES('$option','$value') ON DUPLICATE KEY UPDATE `value`='$value';";
    $db->query($ql);
    if (self::config('env') === 'pro') {
      unlink(LOG_PATH.'/load.php');
    }
  }

  /**
  * Returns modification times in seconds
  * @param $arg (string or array) Indeces
  */
  public static function mt($arg)
  {
    if (!isset(self::$mt)) {
      self::loadMt();
    }
    if (is_array($arg)) {
      $array = [];
      foreach ($arg as $a) {
        $array[] = self::$mt[$a];
      }
      return $array;
    } else {
      return self::$mt[$arg];
    }
  }

  /**
  * Loads modification times from the file
  */
  public static function loadMt()
  {
    if (!isset(self::$mt)) {
      self::$mt = [];
    }
    self::$mt = @include LOG_PATH.'/mt.php';
  }

  /**
  * Updates the modification times for an index/indeces
  * @param $arg (string or array) Indeces
  */
  public static function setMt($arg)
  {
    if (!isset(self::$mt)) {
      self::loadMt();
    }
    if (is_array($arg)) {
      foreach ($arg as $a) {
        self::$mt[$a] = time();
      }
    } else {
      self::$mt[$arg] = time();
    }
    file_put_contents(LOG_PATH.'/mt.php', '<?php return '.var_export(self::$mt, true).';');
  }

  public static function canonical($str)
  {
    View::$canonical = self::config('base').Config::url($str);
  }

  public static function base_url($str = null)
  {
    if (!isset(self::$base_url)) {
      if (isset($_SERVER['HTTP_HOST']) && isset($_SERVER['SCRIPT_NAME'])) {
        $scheme = $_SERVER['REQUEST_SCHEME']??(substr(Config::config('base'), 0, 5)=='https'?'https':'http');
        self::$base_url = $scheme.'://'.$_SERVER['HTTP_HOST'];
        self::$base_url .= substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/')).'/';
      } else {
        self::$base_url = self::config('base');
      }
    }
    if ($str!==null) {
      return self::$base_url.Config::url($str);
    }
    return self::$base_url;
  }

  public static function url($url)
  {
    if ($url==='#') {
      return Router::url().'#';
    }

    if (self::config('rewrite')) {
      $var = explode('/', $url);
      if (Config::config('default-controller') === $var[0]) {
        if ($var[0]!='admin') {
          return substr($url, strlen($var[0])+1);
        }
      }
      return $url;
    }
    $burl = explode('?', $url);
    $burl1 = explode('/', $burl[0]);
    if (isset($burl1[1])) {
      $burl1[1]='&action='.$burl1[1];
    } else {
      $burl1[1]='';
    }
    if (isset($burl[1])) {
      $burl[1]='&'.$burl[1];
    } else {
      $burl[1]='';
    }

    for ($i=2; $i<count($burl1); $i++) {
      if ($burl1[$i]!='') {
        $burl[1]='&var'.($i-1).'='.$burl1[$i].$burl[1];
      }
    }

    return '?c='.$burl1[0].$burl1[1].$burl[1];
  }

  /**
  * Creates a url
  * @param $c (string) Controller name
  * @param $action (string) Action name
  * @param $args (array) Action name
  * @return The full url to print
  */
  public static function make_url($c, $action='', $args=[])
  {
    $params='';
    if (self::config('rewrite')) {
      foreach ($args as $key=>$value) {
        if ($params!='') {
          $params.='/';
        }
        $params.=$value;
      }

      if ((self::config('default-controller') === $c) && ($c != 'admin')) {
        $c='';
      } else {
        $c.='/';
      }
      if ($action!='') {
        $action.='/';
      }
      if ($gpt = Router::request('g_preview_theme')) {
        $params.='?g_preview_theme='.$gpt;
      }
      return $c.$action.$params;
    } else {
      foreach ($args as $key=>$value) {
        $params.='&'.$key.'='.$value;
      }
      if ($gpt = Router::request('g_preview_theme')) {
        $params.='&g_preview_theme='.$gpt;
      }
      return "?c=$c&action=$action$params";
    }
  }

  /**
  * Loads all load files from packages
  */
  public static function load()
  {
    global $db;
    include_once "src/core/load.php";
    foreach ($GLOBALS['config']['packages'] as $package) {
      if (file_exists("src/$package/load.php")) {
        include_once "src/$package/load.php";
      }
    }
    self::$option=[];
    $db->connect();
    $res = $db->get('SELECT `option`,`value` FROM `option`;');
    foreach ($res as $r) {
      self::$option[$r[0]] = $r[1];
    }
    $db->close();
  }

  /**
  * Checks if logged in user has at least one of the required privileges
  * @param $pri (string/array) The privilege(s) to check
  * @return Boolean
  */
  public static function hasPrivilege($pri) // DEPRECATED
  {
    return Session::hasPrivilege($pri);
  }

  /**
  * Creates the folder if does not exist and return the path
  * @param $path (string) Folder path
  */
  public static function dir($path)
  {
    if (file_exists($path)) {
      return $path;
    }
    $p = explode('/', strtr($path, ["\\"=>"/"]));
    $path = '';
    foreach ($p as $folder) {
      if ($folder!=null) {
        $path .= $folder.'/';
        if (!file_exists($path)) {
          mkdir($path, 0755, true);
        }
      }
    }
    return $path;
  }

  public static function tr($key, $alt = null) {
    if(self::$langLoaded===false) {
      foreach(self::$langPaths as $path) self::loadLang($path);
      self::$langLoaded = true;
    }
    if(@isset($GLOBALS['lang'][$key])) {
      if($GLOBALS['lang'][$key] != '')
        return $GLOBALS['lang'][$key];
    }
    if($alt!=null) return $alt;
    return $key;
  }
}

class_alias('Gila\\Config', 'Gila\\Gila');
class_alias('Gila\\Config', 'Gila');
