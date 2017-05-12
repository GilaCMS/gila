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

    static function thumb ($src,$id,$max=180)
    {
        if($src==null) return false;
      $file = 'assets/cache/'.$id;
      $max_width=$max;
      $max_height=$max;
      //return $file;
      //$pathf = explode('\\');
      if($src!='') if (!file_exists($file)) {
        $image = getimagesize($src);
        list($src_width,$src_height)=$image;
        $newwidth=$max_width;
        $newheight=$max_height;

        if($src_width>$max_width) {
          $newheight=($src_height/$src_width)*$newwidth;
        }else if($src_height>$max_height){
          $newwidth=($src_width/$src_height)*$newheight;
        }else{
          copy($src,$file);
          return;
        }

        $tmp=imagecreatetruecolor($newwidth,$newheight);

        switch($image[2]) {
          case 1:
          $img_src = imageCreateFromGIF($src);
          break;
          case 2:
          $img_src = imageCreateFromJPEG($src);
          break;
          case 3:
          $img_src = imageCreateFromPNG($src);
          break;
        }
        imagecopyresampled($tmp,$img_src,0,0,0,0,$newwidth,$newheight,$src_width,$src_height);
imagejpeg($tmp,$file,80);
        /*switch($image[2]) {
          case 1:
          imagegif($tmp,$file);
          break;
          case 2:
          imagejpeg($tmp,$file,100);
          break;
          case 3:
          imagepng($tmp,$file);
          break;
      }*/
        imagedestroy($img_src);
        imagedestroy($tmp);
      }
      return $file;
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
