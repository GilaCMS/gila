<?php

//  common functions will be put here
use core\models\user as user;

class gila {
    static $controller;
    static $widget;
    static $package;
    static $amenu;
    static $widget_area;
    static $option;
    static $privilege;

    function __construct()
    {
		global $db;
        $GLOBALS['version']='1.4.3';
        gila::controllers([
            'admin'=> 'core/controllers/admin',
            'blog'=> 'core/controllers/blog',
            'fm'=> 'core/controllers/fm'
        ]);
        gila::$amenu = [
    	    ['Dashboard','admin','icon'=>'dashboard'],
            'content'=>['Content','admin','icon'=>'newspaper-o','access'=>'editor admin','children'=>[
                ['Pages','admin/pages','icon'=>'file','access'=>'admin'],
                ['Posts','admin/posts','icon'=>'pencil','access'=>'admin writer'],
                ['Categories','admin/postcategories','icon'=>'list','access'=>'admin'],
                ['Media','admin/media','icon'=>'image','access'=>'admin'],
                ['BD Backups','admin/db_backup','icon'=>'database','access'=>'admin'],
                ]],
            'admin'=>['Administration','admin','icon'=>'wrench','access'=>'admin','children'=>[
                ['Users','admin/users','icon'=>'users','access'=>'admin'],
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
    }

    static function controllers($list)
    {
      foreach ($list as $k=>$item) {
            gila::$controller[$k]=$item;
        }
    }

    static function widgets($list)
    {
        foreach ($list as $k=>$item) {
          gila::$widget[$k]=$item;
        }
    }

    static function packages()
    {
        foreach ($list as $k=>$item) {
          gila::$widget[$k]=$item;
        }
    }

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

    static function amenu_child($h,$item)
    {
        if(!isset(gila::$amenu[$h]['children'])) gila::$amenu[$h]['children']=[];
        gila::$amenu[$h]['children'][]=$item;
    }

    static function post($id)
    {

    }

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

    static function setConfig($key, $value)
    {
        $GLOBALS['config'][$key] = $value;
    }

    static function updateConfigFile ()
    {
        $filedata = "<?php\n\n\$GLOBALS['config'] = ".var_export($GLOBALS['config'], true).";";
        //rename('config.php', 'log/config.'.date("Y-m-d").'.php');
        file_put_contents('config.php', $filedata);
    }

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

    static function equal($v1,$v2)
    {
        if (!isset($v1)) return false;
        if (!isset($v2)) return false;
        if ($v1 == $v2) return true;
        return false;
    }

    static function hash($pass)
    {
        return password_hash($pass, PASSWORD_BCRYPT);
    }

	static function option($option,$default='')
    {
        if(isset(gila::$option[$option]) && gila::$option[$option]!='') return gila::$option[$option];
        return $default;
    }

    static function url($url)
    {
        if(gila::config('rewrite')) return $url;
        $burl = explode('?',$url);
        $burl1 = explode('/',$burl[0]);
        if(isset($burl1[1])) $burl1[1]='&action='.$burl1[1]; else $burl1[1]='';
        if(isset($burl[1])) $burl[1]='&'.$burl[1]; else $burl[1]='';

        for($i=2; $i<count($burl1); $i++) {
            if($burl1[$i]!='') $burl[1]='&var'.($i-1).'='.$burl1[$i].$burl[1];
        }

        return gila::config('base').'?c='.$burl1[0].$burl1[1].$burl[1];

    }

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

    static function hasPrivilege ($pri)
    {
        if(!is_array($pri)) $pri=explode(' ',$pri);
        if(!isset($GLOBALS['user_privileges'])) {
            $GLOBALS['user_privileges'] = user::metaList( session::user_id(), 'privilege');
        }

        foreach($pri as $p) if(in_array($p,$GLOBALS['user_privileges'])) return true;
        return false;
    }
}
