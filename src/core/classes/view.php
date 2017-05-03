<?php

class view
{
    private static $part = array();

	static function set($p,$v) {
		self::$part[$p]=$v;
	}

    static function renderAdmin($file, $package = null)
    {
        $path_theme = 'src/core/views/admin';
        include $path_theme."/header.php";
        self::renderFile($file, $package);
        include $path_theme."/footer.php";
    }

    static function render($file, $package = null)
    {
        $path_theme = 'themes/'.gila::config('theme');
        include $path_theme."/header.php";
        self::renderFile($file, $package);
        include $path_theme."/footer.php";
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

        $filePath = 'themes/'.gila::config('theme').'/'.$file; // '/views/'.

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
        $filePath = 'themes/'.gila::config('theme').'/widgets/'.$widget.'.php';
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
        global $db,$widget_data;
        $widgets = $db->get("SELECT * FROM widget WHERE area=? ORDER BY pos ;",[$area]);
        if ($widgets) foreach ($widgets as $widget) {
          $widget_data = $widget['data'];
          $widget_file = "src/".gila::$widget[$widget['widget']]."/{$widget['widget']}.php";
          include $widget_file;
        }
        event::fire($area);
    }
/*
    function displayFile($filepath) {
		$replace = array();
		foreach ($this->part as $key => $value) { $replace[$key] = '{'.$key.'}'; }
		//foreach ($this->part as $key => $value) { $$key = $value; }
		$temp = file_get_contents($filepath);
		echo str_replace($replace, $this->part, $temp);
	}*/
}
