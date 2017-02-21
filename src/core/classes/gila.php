<?php

//  common functions will be put here

class gila {
    static $controller;
    static $widget;
    static $amenu;

    function __construct()
    {
        gila::controllers([
            'admin'=> 'core/controllers/admin',
            'pnk'=> 'core/controllers/pnk',
            'blog'=> 'core/controllers/blog'
        ]);
        gila::amenu([
    		    ['Dashboard','admin','icon'=>'dashboard'],
    		    ['Add-Ons','admin/addons','icon'=>'dropbox'],
    		    ['Posts','admin/posts','icon'=>'pencil'],
    		    ['Users','admin/users','icon'=>'users'],
    		    ['Settings','admin/settings','icon'=>'cogs'],
    		    ['Widgets','admin/widgets','icon'=>'th-large']
        ]);
        gila::widgets([
          'menu'=>'core/widgets/menu',
          'text'=>'core/widgets/text',
          'latest-post'=>'core/widgets/latest-post'
        ]);
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

    static function amenu($list)
    {
        foreach ($list as $k=>$item) {
            gila::$amenu[]=$item;
        }
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
}
