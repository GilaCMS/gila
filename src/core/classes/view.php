<?php

class view
{
  private static $script = array();
  private static $scriptAsync = array();
  private static $css = array();
  private static $cssAsync = false;
  private static $meta = array();
  private static $alert = array();
  public static $part = array();
  public static $stylesheet = array();
  public static $cdn_paths = array();
  public static $view_file = array();
  public static $parent_theme = false;
  public static $canonical;
  public static $renderer;

  static function set($param,$value)
  {
    global $g,$c;
    self::$part[$param]=$value;
    @$g->$param = $value;
    @$c->$param = $value;
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
    if(!self::$cssAsync) {
    ?>
    <script>function loadCSS(f){var c=document.createElement("link");c.rel="stylesheet";c.href=f;document.getElementsByTagName("head")[0].appendChild(c);}</script>
    <?php
    }
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
    echo '<script src="'.$script.'" '.$prop.'></script>';
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
    if($gpt = router::request('g_preview_theme')) return 'themes/'.$gpt;
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

  static function head($head = true)
  {
    echo $head?'<head>':'';
    self::includeFile('head.php');
    echo $head?'</head>':'';
  }

  static function renderFile($filename, $package = 'core')
  {
    $controller = router::controller();
    $action = router::action();
    if(isset(gila::$onaction[$controller][$action])) {
      foreach(gila::$onaction[$controller][$action] as $fn) $fn();
    }
    if(self::includeFile($filename, $package)==false) {
      http_response_code(404);
      self::includeFile('404.php');
    }
  }

  static function includeFile($filename, $package='core')
  {
    global $c;
    $file = self::getViewFile($filename, $package);
    
    if(isset(self::$renderer)) {
      if(self::$renderer($file, self::$part)) {
        return true;
      }
    }

    foreach (self::$part as $key => $value) {
      $$key = $value;
    }

    if($file) {
      if($filename == 'header.php' || $filename  == 'footer.php') {
        include_once $file;
      } else {
        include $file;
      }
      return true;
    }
    return false;
  }

  /**
  * Returns the path of a file inside theme or package folder.
  * @param file (string) The file path.
  * @param package  (string) Optional. The package folder where the file is located if is not found in theme folder.
  */
  static function getViewFile ($file, $package = 'core')
  {
    if(isset(self::$view_file[$file]))
      return 'src/'.self::$view_file[$file].'/views/'.$file;

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

  /**
  * Overrides a view file. Overrides fiel from any package or the theme.
  * @param file (string) Relative path of the view file.
  * @param package  (string) The package folder where the file is located.
  */
  static function setViewFile ($file, $package)
  {
    self::$view_file[$file] = $package;
  }

  /**
  * Displays a menu
  * @param menu (string) Name of the menu. Default=mainmenu
  * @param tpl  (string) Optional. The view template to generate html
  */
  static function menu ($menu='mainmenu', $tpl='tpl/menu.php')
  {
    $file = LOG_PATH.'/menus/'.$menu.'.json';
    if(file_exists($file)) {
      $menu_data = json_decode(file_get_contents($file),true);
    } else {
      $menu_data = core\models\menu::defaultData();
    }
    include self::getViewFile($tpl);
  }

  static function widget ($id,$widget_exp=null)
  {
    global $db,$widget_data;
    if($res = core\models\widget::getById($id)){
      $widget_data = json_decode($res[0]->data);
      $widget_type = $res[0]->widget;
    } else {
      "Widget <b>#".$id."</b> is not found";
      return;
    }

    $widget_file = self::getThemePath().'/widgets/'.$widget_type.'.php';

    if(file_exists($widget_file) == false)
    {
      @$widget_file = "src/".gila::$widget[$type]."/$type.php";
      if(!isset(gila::$widget[$type])) if($type==='text') {
        $widget_file = "src/core/widgets/text/text.php";
      } else {
        echo "Widget <b>".$type."</b> is not found";
      }
    }

    $dir = gila::dir(LOG_PATH.'/cache0/widgets/');
    $_file = $dir.$widget_data->widget_id;
    if(file_exists($_file) ) {
      include $_file;
    } else {
      ob_start();
      @include $widget_file;
      $out2 = ob_get_contents();
      //ob_end_clean();
      $clog = new logger(LOG_PATH.'/cache.log');
      if(file_put_contents($_file,$out2)){
        $clog->debug($_file);
      }else{
        $clog->error($_file);
      }
    }
  }

  /**
  * Display the body of a widget type
  * @param type (string) Name of the widget type
  * @param widget_data  (array) Optional. The data to be used
  * @param widget_file (string) Optional. Alternative wiget view file
  */
  static function widget_body ($type, $widget_data=null, $widget_file=null)
  {
    if($widget_file != null) {
      $widget_file = self::getThemePath().'/widgets/'.$widget_file.'.php';
    } else {
      $widget_file = self::getThemePath().'/widgets/'.$type.'.php';
    }
    if(file_exists($widget_file) == false) {
      @$widget_file = "src/".gila::$widget[$type]."/$type.php";
      if(!isset(gila::$widget[$type])) if($type==='text') {
        $widget_file = "src/core/widgets/text/text.php";
      } else {
        echo "Widget <b>".$type."</b> is not found";
      }
    }
    if(is_object($widget_data)) $data = (array)$widget_data; else $data = &$widget_data;
    @include $widget_file;
  }

  static function block ($path, $widget_data) {
    $block_file = "src/$path/text.php";
    @include $block_file;
  }

  static function blocks (&$blocks) {
    foreach($blocks as $b) {
      if(!is_object($b)) $b = (object)$b;
      //echo '<div class="block '.$b->_type.'">';
      view::widget_body($b->_type, $b);
      //echo '</div>'; 
    }
  }

  /**
  * Dsiplays the widgets of an area
  * @param $area (string) Area name
  * @param $div (optional boolean) If true, widget body will be printed as child of <div class="widget"> item.
  */
  static function widget_area ($area, $div=true, $type=null, $widget_file=null)
  {
    global $widget_data;

    $widgets = core\models\widget::getActiveByArea($area);
    if ($widgets) foreach ($widgets as $widget) {
      if($type != null) if($widget['widget'] != $type) continue;

      $widget_id = json_decode($widget['id']);
      $widget_data = json_decode($widget['data']);
      @$widget_data->widget_id = $widget_id;

      if($div){
        echo '<div class="widget widget-'.$widget['widget'].'" data-id="'.$widget_id.'">';
        if($widget['title']!='') echo '<div class="widget-title">'.$widget['title'].'</div>';
        echo '<div class="widget-body">';
      }

      self::widget_body($widget['widget'], $widget_data);
      if($div) echo '</div></div>';
    }
    event::fire($area);
  }

  static function img ($src, $prefix='', $max=180)
  {
    $pathinfo = pathinfo($src);
    if(strtolower($pathinfo['extension'])=='svg') {
      include $src;
    } else {
      return '<img src="'._url(self::thumb($src, $prefix, $max)).'">';
    }
  }

  static function thumb ($src, $prefix='', $max=180)
  {
    if($src==null) return false;
    $pathinfo = pathinfo($src);
    $ext = strtolower($pathinfo['extension']);
    if(in_array($ext, ['svg','webm'])) return $src;
    $slugify = new Cocur\Slugify\Slugify();

    if(image::imageExtention($ext)==false) return false;
    if(gila::config('use_webp')) {
      if (strpos($_SERVER['HTTP_ACCEPT'], 'image/webp' )!==false) {
        $ext = 'webp';
        $type = IMG_WEBP;
      }
    }
    if(is_numeric($prefix)) {
      $prefix .= '/';
      $max = (int)$prefix;
    }

    $file = SITE_PATH.'tmp/'.$prefix.$slugify->slugify($pathinfo['dirname'].$pathinfo['filename']).'.'.$ext;
    $max_width = $max;
    $max_height = $max;
    if($src=='') return false;
    if (!file_exists($file)) {
      image::make_thumb($src, $file, $max_width, $max_height, $type??null);
    }
    event::fire('view::thumb',[$src,$file]);
    return $file;
  }

  static function thumb_stack ($src_array, $file, $max=180)
  {
    $max_width = $max;
    $max_height = $max;
    if (!file_exists($file) || !file_exists($file.'.json')) {
      return image::make_stack(1,$src_array, $file, $max_width, $max_height);
    }
    $stack = json_decode(file_get_contents($file.'.json'),true);
    if(!is_array($stack[1]))  $stack[1] = [];
    if(is_nan($stack[0]))  $stack[0] = 0;

    foreach($src_array as $key=>$value) {
      $key_src = $stack[1][$key]['src'];
      if($key_src != $value && pathinfo($key_src)['extension']=='jpg') {
        return image::make_stack($stack[0]+1,$src_array, $file, $max_width, $max_height);
      }
    }
    event::fire('view::thumb_stack',[$src_array,$file]);
    return [$file.'?'.$stack[0],$stack[1]];
  }

  static function thumb_xs ($src,$id=null)
  {
    return view::thumb($src,'xs/', 80);
  }
  static function thumb_sm ($src,$id=null)
  {
    return view::thumb($src,'sm/', 200);
  }
  static function thumb_md ($src,$id=null)
  {
    return view::thumb($src,'md/', 400);
  }
  static function thumb_lg ($src,$id=null)
  {
    return self::thumb($src,'lg/', 800);
  }
  static function thumb_xl ($src,$id=null)
  {
    return view::thumb($src,'xl/', 1200);
  }

  static function getTemplates($template) {
    $options = [];

    foreach(self::$view_file as $key => $value) {
      $exploded = explode('--', $key);
      if($exploded[0] == $template){
        $options[] = explode('.', $exploded[1])[0];
      }
    }

    $files = glob(self::getThemePath().'/'.$template.'--*');
    foreach($files as $file) {
      $base = explode('--', $file)[1];
      $options[] = explode('.', $base)[0];
    }

    $files = glob('src/core/views/'.$template.'--*');
    foreach($files as $file) {
      $base = explode('--', $file)[1];
      $options[] = explode('.', $base)[0];
    }

    return array_unique($options);//;
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
