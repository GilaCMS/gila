<?php

class view
{
    private static $part = array();
    private static $stylesheet = array();
    private static $script = array();
    private static $meta = array();

	static function set($p,$v) {
        self::$part[$p]=$v;
	}

    static function meta($m,$v)
    {
        self::$meta[$m]=$v;
    }
    static function stylesheet($v)
    {
        self::$stylesheet[]=$v;
    }
    static function links()
    {
        foreach (self::$stylesheet as $link) echo '<link href="'.$link.'" rel="stylesheet">';
    }

    static function script($v)
    {
        self::$script[]=$v;
    }

    static function getThemePath()
    {
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

    static function head($meta=[])
    {
        echo '<head>';
        echo '<base href="'.gila::config('base').'">';
        echo '<meta charset="utf-8">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
        echo '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
        foreach ($meta as $key=>$value) {
          echo '<meta name="'.$key.'" content="'.$value.'">';
        }
        foreach(self::$meta as $key=>$value) echo '<meta name="'.$key.'" content="'.$value.'">';
        echo '<title>'.gila::config('base').'</title>';
        event::fire('head.meta');
        foreach(self::$stylesheet as $link) echo '<link href="'.$link.'" rel="stylesheet">';

        echo '</head>';
    }

    static function findPath($file, $package = 'core')
    {
        $tpath = self::getThemePath().'/'.$file;
        if(file_exists($tpath)) {
            return $tpath;
        } else {
          $spath = 'src/'.$package.'/views/'.$file;
          if(file_exists($spath)) {
              return $spath;
          }
        }
        return false;
    }

    static function renderFile($file, $package = 'core')
    {
        foreach (self::$part as $key => $value) { $$key = $value; }

        $tpath = self::getThemePath().'/'.$file;
        if(file_exists($tpath)) {
            include $tpath;
            return;
        }else {
          $spath = 'src/'.$package.'/views/'.$file;
          if(file_exists($spath)) {
              include $spath;
          }
        }

        if(router::request('g_response')!='content')
            foreach(self::$script as $src) echo '<script src="'.$src.'"></script>';
	  }

    static function includeFile($filepath,$pack='core')
    {
        $tpath = self::getThemePath().'/'.$filepath;
        if(file_exists($tpath)) {
            include $tpath;
            return;
        }
        $spath = 'src/'.$pack.'/views/'.$filepath;
        if(file_exists($spath)) {
            include $spath;
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
        $widget_data = json_decode($db->get("SELECT data FROM widget WHERE active=1 AND widget=? LIMIT 1;", $widget)[0][0]);
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
    static function widget_area ($area,$div=true)
    {
        global $db,$widget_data;
        $widgets = $db->get("SELECT * FROM widget WHERE active=1 AND area=? ORDER BY pos ;",[$area]);
        if ($widgets) foreach ($widgets as $widget) {
          $widget_data = json_decode($widget['data']);
          $widget_file = "src/".gila::$widget[$widget['widget']]."/{$widget['widget']}.php";
          if($div){
              echo '<div class="widget">';
              if($widget['title']!='') echo '<div class="widget-title">'.$widget['title'].'</div>';
              echo '<div class="widget-body">';
          }
          include $widget_file;
          if($div) echo '</div></div>';
        }
        event::fire($area);
    }

    static function thumb ($src,$id,$max=180)
    {
        if($src==null) return false;
        $file = 'tmp/'.$id;
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
