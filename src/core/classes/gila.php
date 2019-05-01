<?php

/** Common methods for Gila CMS */

class gila
{
  static $controller;
  static $on_controller;
  static $controllerClass;
  static $action;
  static $before;
  static $onaction;
  static $route;
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

  /**
  * Registers new controllers
  * @param $list (Assoc Array) Controllers to register
  * @code
  * gila::controllers( [‘ctrl’=>’my_package/controllers/ctrl’] );
  * @endcode
  */
  static function controllers($list)
  {
    foreach ($list as $k=>$item) {
      gila::$controller[$k]=$item;
    }
  }

  /**
  * Registers new a controller
  * @param $c (string) Controller name as given in url path
  * @param $file (string) Controller's filepath without the php extension
  * @param $name (string) Controller's class name, $c is used by default
  * @code
  * gila::controller('my-ctrl', 'my_package/controllers/ctrl','myctrl');
  * @endcode
  */
  static function controller($c, $file, $name=null)
  {
    gila::$controller[$c] = $file;
    if($name!=null) gila::$controllerClass[$c] = $name;
  }

  /**
  * Registers a function call on a specific path
  * @param $r (string) The path
  * @param $fn (function) Callback for the route
  * @code
  * gila::route('some.txt', function(){ echo 'Some text.'; });
  * @endcode
  */
  static function route($r, $fn)
  {
    gila::$route[$r] = $fn;
  }

  /**
  * Registers a function to run right after the controller class construction
  * @param $c (string) The controller's class name
  * @param $fn (function) Callback
  * @code
  * gila::route('blog', function(){ blog::ppp = 24; });
  * @endcode
  */
  static function onController($c, $fn)
  {
    self::$on_controller[$c][] = $fn;
  }

  /**
  * Registers a new action or replaces an existing for a controller
  * @param $c (string) The controller
  * @param $action (string) The action
  * @param $fn (function) Callback
  * @code
  * gila::action('blog', 'topics', function(){ blog::tagsAction(); });
  * @endcode
  */
  static function action($c, $action, $fn)
  {
    self::$action[$c][$action] = $fn;
  }

  /**
  * Registers a function to run before the function of a specific action
  * @param $c (string) The controller
  * @param $action (string) The action
  * @param $fn (function) Callback
  */
  static function before($c, $action, $fn)
  {
    self::$before[$c][$action][] = $fn;
  }

  static function onAction($c, $action, $fn)
  {
    self::$onaction[$c][$action][] = $fn;
  }

  /**
  * Adds language translations from a json file
  * @param $path (string) Path to the folder/prefix of language json files
  */
  static function addLang($path)
  {
    $filepath = 'src/'.$path.gila::config('language').'.json';
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
    return $GLOBALS['list'][$list];
  }

  /**
  * Registers new widgets
  * @param $list (Assoc Array) Widgets to register
  * @code gila::widgets( [‘wdg’=>’my_package/widgets/wdg’] ); @endcode
  */
  static function widgets($list)
  {
    foreach ($list as $k=>$item) {
      gila::$widget[$k]=$item;
    }
  }

  /**
  * Registers new content type
  * @param $key (string) Name of content type
  * @param $path (string) Path to the table file
  * @code gila::content( 'mytable', 'package_name/content/mytable.php' ); @endcode
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
  * DEPRECIATED @see gila::contentInit()
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
  * @code gila::contentInt( 'mytable', function(&$table) { $table['fileds']['new_field']=[];} ); @endcode
  */
  static function contentInit($key, $init)
  {
    @self::$contentInit[$key][] = $init;
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
  * @code gila::amenu('item', ['Item','controller/action','icon'=>'item-icon']); @endcode
  */
  static function amenu($key,$item=[])
  {
    if(!is_array($key)) $key[$key]=$item;
    foreach ($key as $k=>$i) {
      if(is_numeric($k)) {
        gila::$amenu[]=$i; // depreciated
      } else {
        gila::$amenu[$k]=$i;
      }
    }
  }

  /**
  * Add a child element on administration menu item
  * @param $key (string) Index of parent item
  * @param $item (assoc array) Array with data
  * @code gila::amenu_child('item', ['Child Item','controller/action','icon'=>'item-icon']); @endcode
  */
  static function amenu_child($key,$item)
  {
    if(!isset(gila::$amenu[$key]['children'])) gila::$amenu[$key]['children']=[];
    gila::$amenu[$key]['children'][]=$item;
  }

  /**
  * Sets or gets the value of configuration attribute
  * @param $key (string) Name of the attribute
  * @param $value (optional) The value to set @see setConfig()
  * @return The value if parameter $value is not sent
  */
  static function config($key, $value = null)
  {
    if ($value === null) {
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
    //rename('config.php', 'log/config.'.date("Y-m-d").'.php');
    file_put_contents('config.php', $filedata);
  }

  /**
  * DEPRECIATED @see view::alert()
  */
  static function alert ($type, $msg)
  {
    if ($type == 'alert') $type = '';
    return "<div class='alert $type'><span class='closebtn' onclick='this.parentElement.style.display=\"none\";'>&times;</span>$msg</div>";
  }

  /**
  * Compares two string values. Returns false if any of two is not defined
  * @return Boolean
  */
  static function equal($v1,$v2)
  {
    if (!isset($v1)) return false;
    if (!isset($v2)) return false;
    if ($v1 == $v2) return true;
    return false;
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
    if(isset(gila::$option[$option]) && gila::$option[$option]!='') return gila::$option[$option];
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
    @gila::$option[$option] = $value;
    $ql="INSERT INTO `option`(`option`,`value`) VALUES('$option','$value') ON DUPLICATE KEY UPDATE `value`='$value';";
    $db->query($ql);
    if(gila::config('env')=='pro') unlink('log/load.php');
  }

  /**
  * Returns modification times in seconds
  * @param $arg (string or array) Indeces
  */
  static function mt($arg) {
    if(!isset(self::$mt)) self::loadMt();
    $args = func_get_args();
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
    self::$mt = @include 'log/mt.php';
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
    file_put_contents('log/mt.php', '<?php return '.var_export(self::$mt,true).';');
  }

  static function canonical($str) {
    view::$canonical = gila::config('base').gila::url($str);
  }

  static function base_url($str = null) {
    if(!isset(self::$base_url)) {
      if(isset($_SERVER['REQUEST_URI'])) {
        $scheme = $_SERVER['REQUEST_SCHEME']??(substr(gila::config('base'),0,5)=='https'?'https':'http');
        self::$base_url = $scheme.'://'.$_SERVER['HTTP_HOST'];
        self::$base_url .= substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'],'/')).'/';
      } else {
        self::$base_url = gila::config('base');
      }
    }
    if($str!==null) {
      return self::$base_url.gila::url($str);
    }
    return self::$base_url;
  }

  static function url($url)
  {
    if($url=='#') return router::url().'#';

    if(gila::config('rewrite')) {
      $var = explode('/',$url);
      if(gila::config('default-controller') == $var[0]) if($var[0]!='admin'){
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
    if(gila::config('rewrite')) {
      foreach($args as $key=>$value) {
        if($params!='') $params.='/';
        $params.=$value;
      }

      if((gila::config('default-controller') == $c) && ($c != 'admin')) $c=''; else $c.='/';
      if($action!='') $action.='/';
      if($gpt = router::request('g_preview_theme')) $params.='?g_preview_theme='.$gpt;
      return $c.$action.$params;
    }
    else {
      foreach($args as $key=>$value) {
        $params.='&'.$key.'='.$value;
      }
      if($gpt = router::request('g_preview_theme')) $params.='&g_preview_theme='.$gpt;
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
  	gila::$option=[];
    $db->connect();
  	$res = $db->get('SELECT `option`,`value` FROM `option`;');
  	foreach($res as $r) gila::$option[$r[0]] = $r[1];
    $db->close();
  }

  /**
  * Checks if logged in user has at least one of the required privileges
  * @param $pri (string/array) The privilege(s) to check
  * @return Boolean
  */
  static function hasPrivilege ($pri)
  {
    if(!is_array($pri)) $pri=explode(' ',$pri);
    if(!isset($GLOBALS['user_privileges'])) {
      $GLOBALS['user_privileges'] = core\models\user::permissions(session::user_id());
    }

    foreach($pri as $p) if(@in_array($p,$GLOBALS['user_privileges'])) return true;
    return false;
  }

  /**
  * Creates the folder if does not exist and return the path
  * @param $path (string) Folder path
  */
  static function dir ($path)
  {
    if (file_exists($path)) return $path;
    $p = explode('/', str_replace("\\", "/", $path));
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
  if(@isset($GLOBALS['lang'][$key])) {
    if($GLOBALS['lang'][$key] != '')
      return $GLOBALS['lang'][$key];
  }
  if($alt!=null) return $alt;
  return $key;
}
