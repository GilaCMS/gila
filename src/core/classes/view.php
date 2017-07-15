<?php

class view
{
    private static $part = array();
    private static $stylesheet = array();

	static function set($p,$v) {
		self::$part[$p]=$v;
	}

    static function stylesheet($v)
    {
        self::$stylesheet[]=$v;
    }

    static function getThemePath()
    {
        return 'themes/'.gila::config('theme');
    }

    static function renderAdmin($file, $package = null)
    {
        if(router::post('g_response')=='content') {
            self::renderFile($file, $package);
            return;
        }
        $path_theme = self::getThemePath().'/admin';
        $core_theme = 'src/core/views/admin';
        if(file_exists($path_theme."/header.php")) include $path_theme."/header.php"; else include $core_theme."/header.php";
        self::renderFile($file, $package);
        if(file_exists($path_theme."/footer.php")) include $path_theme."/footer.php"; else include $core_theme."/footer.php";
    }


    static function render($file, $package = null)
    {
        if(router::post('g_response')=='content') {
            self::renderFile($file, $package);
            return;
        }
        $path_theme = self::getThemePath();//'themes/'.gila::config('theme');
        include $path_theme."/header.php";
        self::renderFile($file, $package);
        include $path_theme."/footer.php";
    }

    static function head()
    {
        echo '<head>';
        echo '<base href="'.gila::config('base').'">';
        echo '<meta charset="utf-8">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
        //<meta http-equiv="X-UA-Compatible" content="IE=edge">
        //<meta name="description" content="">
        //<meta name="author" content="">
        echo '<title>'.gila::config('base').'</title>';

        foreach($stylesheet as $link) {
            echo '<link href="'.$link.'" rel="stylesheet">';
        }
        echo '</head>';
    }

    static function renderFile($file, $package = null)
    {
		foreach (self::$part as $key => $value) { $$key = $value; }

        if ($package != null) {
            $filePath = 'src/'.$package.'/views/'.$file;
            if (file_exists($filePath)) {
                include $filePath;
                return;
            }
        }

        $filePath = self::getThemePath().'/'.$file; // '/views/'. gila::config('theme').'/'.

        if (file_exists($filePath)) {
            include $filePath;
        }
        else {
            $filePath = 'src/core/views/'.$file;
            if (file_exists($filePath)) {
                include $filePath;
            } else trigger_error("View file not found ($filePath)");
        }
	  }

/**
 * Widget
 *
 * @widget  name of the widget
 *
 */

    static function widget ($widget,$widget_exp=null)
    {
        global $db,$widget_data;
        $filePath = gila::config('theme').'/widgets/'.$widget.'.php';
        $widget_data = $db->get("SELECT data FROM widget WHERE widget=? LIMIT 1;", $widget)[0][0];
        if($widget_exp==null) $widget_exp=$widget;

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
    static function widget_area ($area)
    {
        global $db,$widget_data;
        $widgets = $db->get("SELECT * FROM widget WHERE area=? ORDER BY pos ;",[$area]);
        if ($widgets) foreach ($widgets as $widget) {
          $widget_data = $widget['data'];
          $widget_file = "src/".gila::$widget[$widget['widget']]."/{$widget['widget']}.php";
          include $widget_file;
        }
        event::fire($area);
    }

    static function thumb ($src,$id,$max=180)
    {
        if($src==null) return false;
        $file = 'assets/cache/'.$id;
        $max_width=$max;
        $max_height=$max;
        if($src=='') return false;
        if (!file_exists($file)) {
            image::make_thumb($src,$file,$max_width,$max_height);
        }
        return $file;
    }

    static function thumb_xs ($src,$id)
    {
        return view::thumb($src,$id,80);
    }
    static function thumb_sm ($src,$id)
    {
        return view::thumb($src,$id,160);
    }
    static function thumb_md ($src,$id)
    {
        return view::thumb($src,$id,320);
    }
    static function thumb_lg ($src,$id)
    {
        return self::thumb($src,$id,640);
    }
    static function thumb_xl ($src,$id)
    {
        return view::thumb($src,$id,1200);
    }

}
