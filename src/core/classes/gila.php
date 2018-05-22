<?php

/** Common methods for Gila CMS */
use core\models\user as user;

class gila {
    static $controller;
    static $on_controller;
    static $controllerClass;
    static $action;
    static $before;
    static $route;
    static $widget;
    static $package;
    static $amenu;
    static $widget_area;
    static $option;
    static $privilege;
    static $content;

    function __construct()
    {

    }

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
    * gila::route('some.txt', function(){ return 'Some text.'; });
    * @endcode
    */
    static function route($r, $fn)
    {
        gila::$route[$r] = $fn;
    }

    /**
    * Registers a function to run right after the controller class construction
    * @param $c (string) The controller
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

    /**
    * Adds language translations from a json file
    * @param $path (string) Path to the folder/prefix of language json files
    */
    static function addLang($path)
    {
        $filepath = 'src/'.$path.gila::config('language').'.json';
        if(file_exists($filepath)) {
            $GLOBALS['lang'] = array_merge($GLOBALS['lang'],
                json_decode(file_get_contents($filepath),true)
            );
        }
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
    * Returns the list of active packages
    * @return Array
    */
    static function packages()
    {
        return $GLOBALS['config']['packages'];
    }

    /**
    * Add new elements on administration menu
    * @param $list (string) Index name
    * @param $list (assoc array) Array with data<br>
    * Indices 0 for Display name, 1 for action link
    * @code gila::amenu('item', ['Item','controller/action','icon'=>'item-icon']); @endcode
    */
    static function amenu($list,$arg2=[])
    {
        if(!is_array($list)) $list[$list]=$arg2;
        foreach ($list as $k=>$item) {
            if(is_numeric($k)) {
                gila::$amenu[]=$item; // depreciated
            } else {
                gila::$amenu[$k]=$item;
            }
        }
    }

    /**
    * Add a child element on administration menu item
    * @param $key (string) Index of parent item
    * @param $value (assoc array) Array with data<br>
    * @code gila::amenu_child('item', ['Child Item','controller/action','icon'=>'item-icon']); @endcode
    */
    static function amenu_child($h,$item)
    {
        if(!isset(gila::$amenu[$h]['children'])) gila::$amenu[$h]['children']=[];
        gila::$amenu[$h]['children'][]=$item;
    }

    static function post($id)
    {

    }

    /**
    * Sets or gets the value of configuration attribute
    * @param $key (string) Name of the attribute
    * @param $value (optional) The value to set @see setConfig()
    * @return The value if parameter $value is not sent
    */
    static function config($key, $value = null)
    {
        if ($value == null) {
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
        $GLOBALS['config'][$key] = $value;
    }

    /**
    * Rewrites the config.php file
    */
    static function updateConfigFile ()
    {
        $filedata = "<?php\n\n\$GLOBALS['config'] = ".var_export($GLOBALS['config'], true).";";
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

    static function menu($id = null)
    {
        global $db;

        $data = json_decode( $db->value("SELECT data FROM widget WHERE widget='menu' LIMIT 1"),true);

        foreach ($data as $k=>$d) {
            if (isset($d['children'])) {
                if (is_array($d['children'])) $data[$k]['children'] = $d['children'][0];
                if (count($data[$k]['children'])==0) unset($data[$k]['children']);
            }
        }

        if ($id == null) return $data;
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
	static function option($option,$default='')
    {
        if(isset(gila::$option[$option]) && gila::$option[$option]!='') return gila::$option[$option];
        return $default;
    }

    static function url($url)
    {
        if(gila::config('rewrite')) {
            $var = explode('/',$url);
            if(gila::config('default-controller') == $var[0]) {
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

        return gila::config('base').'?c='.$burl1[0].$burl1[1].$burl[1];

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

            if(gila::config('default-controller') == $c) $c='';
            if($action!='') {
                if($c!='') $c.='/';
                $action.='/';
            }
            if(isset($_GET['g_preview_theme'])) $params.='?g_preview_theme='.$_GET['g_preview_theme'];
            return gila::config('base').$c.$action.$params;
        }
        else {
            foreach($args as $key=>$value) {
                $params.='&'.$key.'='.$value;
            }
            if(isset($_GET['g_preview_theme'])) $params.='&g_preview_theme='.$_GET['g_preview_theme'];
            return gila::config('base')."?c=$c&action=$action$params";
        }

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
            $GLOBALS['user_privileges'] = user::metaList( session::user_id(), 'privilege');
        }

        foreach($pri as $p) if(in_array($p,$GLOBALS['user_privileges'])) return true;
        return false;
    }

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

function __($slug) {
    if(@isset($GLOBALS['lang'][$slug])) {
        if($GLOBALS['lang'][$slug] != '')
            return $GLOBALS['lang'][$slug];
    }
    return $slug;
}
