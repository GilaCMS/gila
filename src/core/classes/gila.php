<?php

//  common functions will be put here

class gila {
    static $controller;
    static $widget;
    static $package;
    static $amenu;
    static $widget_area;
    static $option;

    function __construct()
    {
		global $db;
        $GLOBALS['version']='1.2.0';
        gila::controllers([
            'admin'=> 'core/controllers/admin',
            'blog'=> 'core/controllers/blog'
        ]);
        gila::$amenu = [
    	    ['Dashboard','admin','icon'=>'dashboard'],
            'context'=>['Content','admin','icon'=>'newspaper-o','access'=>'writer','children'=>[
                ['Pages','admin/pages','icon'=>'file','access'=>'admin'],
                ['Posts','admin/posts','icon'=>'pencil','access'=>'admin writer'],
                ['Categories','admin/postcategories','icon'=>'list','access'=>'admin'],
                ['Media','admin/media','icon'=>'image','access'=>'admin'],
                ['BD Backups','admin/db_backup','icon'=>'database','access'=>'admin'],
                ]],
            'admin'=>['Administration','admin','icon'=>'wrench','access'=>'admin','children'=>[
                ['Users','admin/users','icon'=>'users','access'=>'admin'],
                ['Widgets','admin/widgets','icon'=>'th-large','access'=>'admin'],
                ['Add-Ons','admin/addons','icon'=>'dropbox','access'=>'admin'],
                ['Themes','admin/themes','icon'=>'paint-brush','access'=>'admin'],
                ['Settings','admin/settings','icon'=>'cogs','access'=>'admin'],
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

    static function amenu($list)
    {
        foreach ($list as $k=>$item) {
            gila::$amenu[]=$item;
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
            return $GLOBALS['config'][$key];
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
        if(isset(gila::$option[$option])) return gila::$option[$option];
        return $default;
    }

    static function make_url($c, $action='', $args=[])
    {
        $params='';
        foreach($args as $key=>$value) {
            $params.='/'.$value;
        }
		    if(router::controller()==$c) $c.='';
        if($action!='') if($c!='') $c.='/';
        return gila::config('base').$c.$action.$params;
		/*
        foreach($args as $key=>$value) {
            $params.='&'.$key.'='.$value;
        }
        return gila::config('base')."?c=$c&action=$action$params";
		*/
    }
}
