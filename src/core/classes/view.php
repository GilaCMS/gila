<?php

class view
{
    private static $part = array();
    private static $stylesheet = array();
    private static $script = array();
    private static $scriptAsync = array();
    private static $css = array();
    private static $cssAsync = array();
    private static $meta = array();
    private static $alert = array();
    public static $cdn_paths = array();
    public static $parent_theme = false;

	static function set($param,$value) {
        global $g;
        self::$part[$param]=$value;
        @$g->$param = $value;
	}

    /**
    * Set a meta value
    */
    static function meta($meta,$value)
    {
        self::$meta[$meta]=$value;
    }

    static function stylesheet($href)
    {
        if(in_array($href,self::$stylesheet)) return;
        self::$stylesheet[]=$href;
    }

    static function links()
    {
        foreach (self::$stylesheet as $link) echo '<link href="'.$link.'" rel="stylesheet">';
    }

    static function scripts()
    {
        //foreach(self::$script as $src) echo '<script src="'.$src.'"></script>';
    }

    /**
    * Set an alert message
    */
    static function alert($type,$msg)
    {
        self::$alert[]=[$type,$msg];
    }

    static function alerts()
    {
        foreach (self::$alert as $a) echo '<div class="alert '.$a[0].'"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>'.$a[1].'</div>';
    }

    /**
    * Adds a css file inline
    * @param $css Path to css file
    */
    static function cssInline($css)
    {
        echo '<style>'.file_get_contents($css).'</style>';
    }

    /**
    * Adds a link tag of css file
    * @param $css Path to css file
    */
    static function css($css)
    {
        if(in_array($css,self::$css)) return;
        self::$css[]=$css;
        echo '<link rel="stylesheet" href="'.$css.'">';
    }

    /**
    * Loads a css file asynchronously using a simple javascript function
    * @param $css Path to css file
    */
    static function cssAsync($css)
    {
        if(in_array($css,self::$css)) return;
        self::$css[]=$css;
        ?><script>function loadCSS(f){var c=document.createElement("link");c.rel="stylesheet";c.href=f;document.getElementsByTagName("head")[0].appendChild(c);}</script><?php
        echo '<script>loadCSS("'.$css.'");</script>';
    }

    /**
    * Adds a script tag of javascript file
    * @param $script Path to js file
    */
    static function script($script, $prop = '')
    {
        if(in_array($script,self::$script)) return;
        self::$script[]=$script;
        if(gila::config('use_cdn')=='1' && isset(self::$cdn_paths[$script]))
            $script = self::$cdn_paths[$script];
        echo '<script src="'.$script.' '.$prop.'"></script>';
    }

    /**
    * Adds a script tag of javascript file lo load asynchronously
    * @param $script Path to js file
    */
    static function scriptAsync($script)
    {
        if(in_array($script,self::$scriptAsync)) return;
        self::$scriptAsync[]=$script;
        self::script($script, 'async');
    }

    /**
    * Returns the relative path of the selected theme's folder
    * @return string
    */
    static function getThemePath()
    {
        if(isset($_GET['g_preview_theme'])) return 'themes/'.$_GET['g_preview_theme'];
        return 'themes/'.gila::config('theme');
    }

    static function renderAdmin($file, $package = 'core')
    {
        if(router::request('g_response')=='content') {
            self::renderFile($file, $package);
            return;
        }

        self::includeFile('admin/header.php');
        self::renderFile($file, $package);
        self::includeFile('admin/footer.php');
    }


    static function render($file, $package = 'core')
    {
        if(router::request('g_response')=='json') {
            foreach (self::$part as $key => $value) if(is_object($value)) {
                self::$part[$key]=[];
                foreach($value as $r) {
                    self::$part[$key][]=(array)$r;
                }
            }
            echo json_encode(self::$part);
            exit;
        }

        if(router::request('g_response')=='content') {
            self::renderFile($file, $package);
            return;
        }
        self::includeFile('header.php');
        self::renderFile($file, $package);
        self::includeFile('footer.php');
    }

    static function head()
    {
        self::includeFile('head.php');
    }

    static function updateC() {
        global $c;
        foreach (self::$part as $key => $value) {
            $$key = $value;
            @$c->$key = $value;
        }
    }

    static function renderFile($file, $package = 'core')
    {
        global $c;
        foreach (self::$part as $key => $value) {
            $$key = $value;
            @$c->$key = $value;
        }

        if($file = self::getViewFile($file, $package)) {
            include $file;
            return;
        }

        self::includeFile('404.php');
	}

    static function includeFile($file,$package='core')
    {
        global $c;
        foreach (self::$part as $key => $value) {
            $$key = $value;
        }

        if($file = self::getViewFile($file, $package)) {
            include $file;
            return;
        }
        return false;
    }

    static function getViewFile ($file, $package = 'core') {
        $tpath = self::getThemePath().'/'.$file;
        if(file_exists($tpath)) return $tpath;

        if(self::$parent_theme) {
            $tpath = 'themes/'.self::$parent_theme.'/'.$file;
            if(file_exists($tpath)) return $tpath;
        }

        $spath = 'src/'.$package.'/views/'.$file;
        if(file_exists($spath)) return $spath;

        return false;
    }

    static function menu ($menu='mainmenu', $tpl='tpl/menu.php')
    {
        $file = 'log/menus/'.$menu.'.json';
        if(file_exists($file)) {
            $menu_data = json_decode(file_get_contents($file),true);
        } else {
            $menu_data = core\models\menu::defaultData();
        }
        include self::getViewFile($tpl);
    }

    /**
    * Widget
    * @param widget (string) Name of the widget type
    *
    */

    static function widget ($widget,$widget_exp=null)
    {
        global $db,$widget_data;
        if($widget_exp==null) $widget_exp=$widget;
        $mm = gila::config('default.'.$widget);
        if($mm > 0) {
            $res = $db->get("SELECT data FROM widget WHERE id=?;",[$mm])[0];
            $widget_data = json_decode($res['data']);
        } else {
            $widget_data = null;
        }

        $filePath = self::getThemePath().'/widgets/'.$widget.'.php';

        if (file_exists($filePath)) {
            include $filePath;
        }
        else {
            $filePath = 'src/core/widgets/'.$widget.'/'.$widget_exp.'.php';
            if (file_exists($filePath)) {
                include $filePath;
            }
            else {
                echo $filePath." file not found!";
            }
        }
    }

    static function block ($area)
    {
        view::widget_area($area);
    }

    /**
    * Dsiplays the widgets of an area
    * @param $area (string) Area name
    * @param $div (optional boolean) If true, widget body will be printed as child of <div class="widget"> item.
    */
    static function widget_area ($area,$div=true)
    {
        global $db,$widget_data;
        $widgets = $db->get("SELECT * FROM widget WHERE active=1 AND area=? ORDER BY pos ;",[$area]);
        if ($widgets) foreach ($widgets as $widget) {
            $widget_id = json_decode($widget['id']);
            $widget_data = json_decode($widget['data']);

            if($div){
                echo '<div class="widget">';
                if($widget['title']!='') echo '<div class="widget-title">'.$widget['title'].'</div>';
                echo '<div class="widget-body">';
            }

            $widget_file = self::getThemePath().'/widgets/'.$widget['widget'].'.php';
            if(file_exists($widget_file) == false) {
                @$widget_file = "src/".gila::$widget[$widget['widget']]."/{$widget['widget']}.php";
                if(!isset(gila::$widget[$widget['widget']])) echo "Widget <b>".$widget['widget']."</b> is not found";
            }

            @include $widget_file;

            if($div) echo '</div></div>';
        }
        event::fire($area);
    }

    static function thumb ($src, $prefix, $max=180)
    {
        if($src==null) return false;
        $file = 'tmp/'.str_replace(["://",":\\\\","\\","/",":"], "/", $prefix.$src);
        $max_width = $max;
        $max_height = $max;
        if($src=='') return false;
        if (!file_exists($file)) {
            image::make_thumb($src,$file,$max_width,$max_height);
        }
        return $file;
    }

    static function thumb_stack ($src_array, $file, $max=180)
    {
        $max_width = $max;
        $max_height = $max;
        if (!file_exists($file) || !file_exists($file.'.json')) {
            return image::make_stack($src_array, $file, $max_width, $max_height);
        }
        $stack = json_decode(file_get_contents($file.'.json'),true);
        foreach($src_array as $key=>$value) {
            if($stack[$key]['src'] != $value) {
                return image::make_stack($src_array, $file, $max_width, $max_height);
            }
        }
        return $stack;
    }

    static function thumb_xs ($src,$id=null)
    {
        return view::thumb($src,'xs/',80);
    }
    static function thumb_sm ($src,$id=null)
    {
        return view::thumb($src,'sm/',160);
    }
    static function thumb_md ($src,$id=null)
    {
        return view::thumb($src,'md/',320);
    }
    static function thumb_lg ($src,$id=null)
    {
        return self::thumb($src,'lg/',640);
    }
    static function thumb_xl ($src,$id=null)
    {
        return view::thumb($src,'xl/',1200);
    }

    /**
    * $srcset = view::thumb_srcset($src);
    * @example background-image: -webkit-image-set(url({$srcset[0]}) 1x, url({$srcset[1]}) 2x);
    * @example <img srcset="{$srcset[0]}, {$srcset[0]} 2x" src="{$srcset[0]}"
    */
    static function thumb_srcset ($src, $sizes = [1200,320])
    {
        $r = [];
        foreach($sizes as $w) {
            $r[] = view::thumb($src, $w.'/', $w);
        }
        return $r;
    }

}
