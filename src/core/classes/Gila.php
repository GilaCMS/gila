<?php

/** Common methods for Gila CMS */

class Gila
{
  static $widget;
  static $package;
  static $amenu;
  static $widget_area = [];
  static $option;
  static $privilege;
  static $content;
  static $contentField;
  static $contentInit = [];
  static $mt;
  static $base_url;
  static $langPaths = [];
  static $langLoaded = false;

  /**
  * Registers new a controller
  * @param $c (string) Controller name as given in url path
  * @param $file (string) Controller's filepath without the php extension
  * @param $name (string) Controller's class name, $c is used by default
  * @code
  * Gila::controller('my-ctrl', 'my_package/controllers/ctrl','myctrl');
  * @endcode
  */
  static function controller($c, $path, $name=null) // DEPRECATED 
  {
    Router::controller($c, $path);
  }

  /**
  * Registers a function call on a specific path
  * @param $r (string) The path
  * @param $fn (function) Callback for the route
  * @code
  * Gila::route('some.txt', function(){ echo 'Some text.'; });
  * @endcode
  */
  static function route($r, $fn) // DEPRECATED
  {
    Router::add($r, $fn);
  }

  /**
  * Registers a function to run right after the controller class construction
  * @param $c (string) The controller's class name
  * @param $fn (function) Callback
  * @code
  * Gila::onController('blog', function(){ BlogCtrl::ppp = 24; });
  * @endcode
  */
  static function onController($c, $fn) // DEPRECATED
  {
    Router::$on_controller[$c][] = $fn;
  }

  /**
  * Registers a new action or replaces an existing for a controller
  * @param $c (string) The controller
  * @param $action (string) The action
  * @param $fn (function) Callback
  * @code
  * Gila::action('blog', 'topics', function(){ ... });
  * @endcode
  */
  static function action($c, $action, $fn) // DEPRECATED -> Router::action()
  {
    Router::action($c, $action, $fn);
  }

  /**
  * Registers a function to run before the function of a specific action
  * @param $c (string) The controller
  * @param $action (string) The action
  * @param $fn (function) Callback
  */
  static function before($c, $action, $fn) // DEPRECATED -> Router::before()
  {
    Router::before($c, $action, $fn);
  }

  static function onAction($c, $action, $fn) // DEPRECATED -> Router::onAction()
  {
    Router::onAction($c, $action, $fn);
  }

  /**
  * Adds language translations from a json file
  * @param $path (string) Path to the folder/prefix of language json files
  */
  static function addLang($path)
  {
    if(in_array($path, self::$langPaths)) return;

    if(self::$langLoaded===true) {
      self::loadLang($path);
    }
    self::$langPaths[] = $path;
  }

  static function loadLang($path)
  {
    $filepath = 'src/'.$path.Gila::config('language').'.json';
    if(file_exists($filepath)) {
      $GLOBALS['lang'] = @array_merge(@$GLOBALS['lang'],
        json_decode(file_get_contents($filepath),true)
      );
    }
  }

  static function addList($list, $el)
  {
    @$GLOBALS['list'][$list][] = $el;
  }

  static function getList($list)
  {
    return $GLOBALS['list'][$list] ?? [];
  }

  /**
  * Registers new widgets
  * @param $list (Assoc Array) Widgets to register
  * @code Gila::widgets( [‘wdg’=>’my_package/widgets/wdg’] ); @endcode
  */
  static function widgets($list)
  {
    foreach ($list as $k=>$item) {
      Gila::$widget[$k]=$item;
    }
  }

  /**
  * Registers new content type
  * @param $key (string) Name of content type
  * @param $path (string) Path to the table file
  * @code Gila::content( 'mytable', 'package_name/content/mytable.php' ); @endcode
  */
  static function content($key, $path)
  {
    self::$content[$key] = $path;
  }

  /**
  * Make changes of a field of content type
  * @param $key (string) Name of content type
  * @param $field (string) Index of the field
  * @param $table (Assoc array) Value of the field
  * DEPRECATED @see Gila::contentInit()
  */
  static function contentField($key, $field, $table)
  {
    if(!isset(self::$contentField[$key])) self::$contentField[$key] = [];
    self::$contentField[$key][$field] = $table;
  }

  /**
  * Make changes in a content type when it is initialized
  * @param $key (string) Name of content type
  * @param $init (function) Funtion to run
  * @code Gila::contentInt( 'mytable', function(&$table) { $table['fileds']['new_field']=[];} ); @endcode
  */
  static function contentInit($key, $init)
  {
    @self::$contentInit[$key][] = $init;
    if(isset(gTable::$tableList[$key])) {
      unset(gTable::$tableList[$key]);
    }
  }

  /**
  * Returns the list of active packages
  * @return Array
  */
  static function packages()
  {
    return $GLOBALS['config']['packages'];
  }

  /**
  * Add new elements on administration menu
  * @param $key (string) Index name
  * @param $item (assoc array) Array with data
  * Indices 0 for Display name, 1 for action link
  * @code Gila::amenu('item', ['Item','controller/action','icon'=>'item-icon']); @endcode
  */
  static function amenu($key,$item=[])
  {
    if(!is_array($key)) {
      $list = [];
      $list[$key] = $item;
    } else $list = $key;
    foreach ($list as $k=>$i) {
      if(is_numeric($k)) {
        Gila::$amenu[]=$i; // DEPRECATED
      } else {
        Gila::$amenu[$k]=$i;
      }
    }
  }

  /**
  * Add a child element on administration menu item
  * @param $key (string) Index of parent item
  * @param $item (assoc array) Array with data
  * @code Gila::amenu_child('item', ['Child Item','controller/action','icon'=>'item-icon']); @endcode
  */
  static function amenu_child($key,$item)
  {
    if(!isset(Gila::$amenu[$key]['children'])) Gila::$amenu[$key]['children']=[];
    Gila::$amenu[$key]['children'][]=$item;
  }

  /**
  * Sets or gets the value of configuration attribute
  * @param $key (string) Name of the attribute
  * @param $value (optional) The value to set @see setConfig()
  * @return The value if parameter $value is not sent
  */
  static function config($key, $value = null)
  {
    if ($value === null) { // DEPRECATED should use setConfig()
      if(isset($GLOBALS['config'][$key])) {
        return $GLOBALS['config'][$key];
      }
      else return null;
    }
    else {
      $GLOBALS['config'][$key] = $value;
    }
  }

  /**
  * Sets the value of configuration attribute
  * @param $key (string) Name of the attribute
  * @param $value (optional) The value to set
  */
  static function setConfig($key, $value)
  {
    if(!is_string($key)) return;
    $GLOBALS['config'][$key] = $value;
  }

  /**
  * Rewrites the config.php file
  */
  static function updateConfigFile ()
  {
    $filedata = "<?php\n\n\$GLOBALS['config'] = ".var_export($GLOBALS['config'],true).";";
    file_put_contents(CONFIG_PHP, $filedata);
  }

  /**
  * DEPRECATED @see View::alert()
  */
  static function alert ($type, $msg)
  {
    if ($type === 'alert') $type = '';
    return "<div class='alert $type'><span class='closebtn' onclick='this.parentElement.style.display=\"none\";'>&times;</span>$msg</div>";
  }

  /**
  * @return Password hash
  */
  static function hash($pass)
  {
    return password_hash($pass, PASSWORD_BCRYPT);
  }

  /**
  * Returns an option value
  * @param $option (string) Option name
  * @param $default (optional) The value to return if this option has not saved value
  * @return The option value
  */
  static function option($option, $default='')
  {
    if(isset(Gila::$option[$option]) && Gila::$option[$option]!='') return Gila::$option[$option];
    return $default;
  }

  /**
  * Sets an option value
  * @param $option (string) Option name
  * @param $value (optional) The value to set
  */
  static function setOption($option, $value='')
  {
    global $db;
    @Gila::$option[$option] = $value;
    $ql="INSERT INTO `option`(`option`,`value`) VALUES('$option','$value') ON DUPLICATE KEY UPDATE `value`='$value';";
    $db->query($ql);
    if(Gila::config('env') === 'pro') unlink(LOG_PATH.'/load.php');
  }

  /**
  * Returns modification times in seconds
  * @param $arg (string or array) Indeces
  */
  static function mt($arg) {
    if(!isset(self::$mt)) self::loadMt();
    if(is_array($arg)) {
      $array = [];
      foreach($arg as $a) $array[] = self::$mt[$a];
      return $array;
    } else return self::$mt[$arg];
  }

  /**
  * Loads modification times from the file
  */
  static function loadMt() {
    if(!isset(self::$mt)) self::$mt = [];
    self::$mt = @include LOG_PATH.'/mt.php';
  }

  /**
  * Updates the modification times for an index/indeces
  * @param $arg (string or array) Indeces
  */
  static function setMt($arg) {
    if(!isset(self::$mt)) self::loadMt();
    if(is_array($arg)) {
      foreach($arg as $a) self::$mt[$a] = time();
    } else self::$mt[$arg] = time();
    file_put_contents(LOG_PATH.'/mt.php', '<?php return '.var_export(self::$mt,true).';');
  }

  static function canonical($str) {
    View::$canonical = Gila::config('base').Gila::url($str);
  }

  static function base_url($str = null) {
    if(!isset(self::$base_url)) {
      if(isset($_SERVER['HTTP_HOST']) && isset($_SERVER['SCRIPT_NAME'])) {
        $scheme = $_SERVER['REQUEST_SCHEME']??(substr(Gila::config('base'),0,5)=='https'?'https':'http');
        self::$base_url = $scheme.'://'.$_SERVER['HTTP_HOST'];
        self::$base_url .= substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'],'/')).'/';
      } else {
        self::$base_url = Gila::config('base');
      }
    }
    if($str!==null) {
      return self::$base_url.Gila::url($str);
    }
    return self::$base_url;
  }

  static function url($url)
  {
    if($url==='#') return Router::url().'#';

    if(Gila::config('rewrite')) {
      $var = explode('/',$url);
      if(Gila::config('default-controller') === $var[0]) if($var[0]!='admin'){
        return substr($url, strlen($var[0])+1);
      }
      return $url;
    }
    $burl = explode('?',$url);
    $burl1 = explode('/',$burl[0]);
    if(isset($burl1[1])) $burl1[1]='&action='.$burl1[1]; else $burl1[1]='';
    if(isset($burl[1])) $burl[1]='&'.$burl[1]; else $burl[1]='';

    for($i=2; $i<count($burl1); $i++) {
      if($burl1[$i]!='') $burl[1]='&var'.($i-1).'='.$burl1[$i].$burl[1];
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
  static function make_url($c, $action='', $args=[])
  {
    $params='';
    if(Gila::config('rewrite')) {
      foreach($args as $key=>$value) {
        if($params!='') $params.='/';
        $params.=$value;
      }

      if((Gila::config('default-controller') === $c) && ($c != 'admin')) $c=''; else $c.='/';
      if($action!='') $action.='/';
      if($gpt = Router::request('g_preview_theme')) $params.='?g_preview_theme='.$gpt;
      return $c.$action.$params;
    }
    else {
      foreach($args as $key=>$value) {
        $params.='&'.$key.'='.$value;
      }
      if($gpt = Router::request('g_preview_theme')) $params.='&g_preview_theme='.$gpt;
      return "?c=$c&action=$action$params";
    }

  }

  /**
  * Loads all load files from packages
  */
  static function load ()
  {
    global $db;
    include_once "src/core/load.php";
  	foreach ($GLOBALS['config']['packages'] as $package) {
  		if(file_exists("src/$package/load.php")) include_once "src/$package/load.php";
  	}
  	Gila::$option=[];
    $db->connect();
  	$res = $db->get('SELECT `option`,`value` FROM `option`;');
  	foreach($res as $r) Gila::$option[$r[0]] = $r[1];
    $db->close();
  }

  /**
  * Checks if logged in user has at least one of the required privileges
  * @param $pri (string/array) The privilege(s) to check
  * @return Boolean
  */
  static function hasPrivilege ($pri) // DEPRECATED
  {
    return Session::hasPrivilege($pri);
  }

  /**
  * Creates the folder if does not exist and return the path
  * @param $path (string) Folder path
  */
  static function dir ($path)
  {
    if (file_exists($path)) return $path;
    $p = explode('/', strtr($path, ["\\"=>"/"]));
    $path = '';
    foreach ($p as $folder) if($folder!=null){
      $path .= $folder.'/';
      if (!file_exists($path)) {
        mkdir($path,0755,true);
      }

    }
    return $path;
  }

}

$GLOBALS['lang'] = [];

function __($key, $alt = null) {
  if(Gila::$langLoaded===false) {
    foreach(Gila::$langPaths as $path) Gila::loadLang($path);
    Gila::$langLoaded = true;
  }
  if(@isset($GLOBALS['lang'][$key])) {
    if($GLOBALS['lang'][$key] != '')
      return $GLOBALS['lang'][$key];
  }
  if($alt!=null) return $alt;
  return $key;
}

function _url($url) {
  return str_replace(['\'','"','<','>',':'], ['%27','%22','%3C','%3E','%3A'], $url);
}
