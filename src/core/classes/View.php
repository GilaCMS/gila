<?php

namespace Gila;

class View
{
  private static $script = [];
  private static $scriptAsync = [];
  private static $meta = [];
  private static $onrender = false;
  public static $alert = [];
  public static $css = [];
  public static $part = [];
  public static $stylesheet = [];
  public static $view_file = [];
  public static $view_path = [];
  public static $parent_theme = false;
  public static $canonical;
  public static $renderer;
  public static $cdn_host = '';
  public static $cdn_paths = [
    'core/gila.min.js'=> 'core/gila2224.js',
    'core/gila.js'=> 'core/gila2224.js',
    'core/gila.min.css'=> 'core/gila1501.css',
    'core/gila.css'=> 'core/gila1501.css',
    'core/widgets.css'=> 'core/widgets1027.css',
    'core/admin/media.js'=> 'core/admin/media1806.js',
    'core/admin/content.js'=> 'core/admin/content0112.js',
    'core/admin/content.css'=> 'core/admin/content1215.css',
    'core/admin/vue-components.js'=> 'core/admin/vue-components1001.js'
  ];

  public static function set($param, $value=null)
  {
    if (is_array($param)) {
      self::$part = array_merge(self::$part, $param);
    } else {
      self::$part[$param]=$value;
    }
  }

  public static function get($param)
  {
    return self::$part[$param]??null;
  }

  /**
  * Set a meta value
  */
  public static function meta($meta, $value)
  {
    self::$meta[$meta]=$value;
  }

  public static function stylesheet($href)
  {
    if (isset(self::$cdn_paths[$href])) {
      $href = self::$cdn_paths[$href];
    }
    if (file_exists('assets/'.$href)) {
      $href = self::$cdn_host.'assets/'.$href;
    }
    if (in_array($href, self::$stylesheet)) {
      return;
    }
    self::$stylesheet[]=$href;
  }

  /**
  * Set an alert message
  */
  public static function alert($type, $msg)
  {
    self::$alert[]=[$type,$msg];
  }

  public static function alerts()
  {
    foreach (self::$alert as $a) {
      echo '<div class="alert '.$a[0].'"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>'.$a[1].'</div>';
    }
  }

  /**
  * Adds a css file inline
  * @param $css Path to css file
  */
  public static function cssInline($css)
  {
    return '<style>'.file_get_contents(self::cssPath($css)).'</style>';
  }

  /**
  * Adds a link tag of css file
  * @param $css Path to css file
  */
  public static function css($css, $prop='')
  {
    if ($href = self::cssPath($css)) {
      echo '<link rel="stylesheet" href="'.$href.'" '.$prop.'>';
    }
  }

  public static function cssAsync($css, $uri=false)
  {
    if ($href = self::cssPath($css)) {
      echo '<script>function loadCSS(f){var c=document.createElement("link");c.rel="stylesheet";c.href=f;document.getElementsByTagName("head")[0].appendChild(c);}</script>';
      echo '<script>loadCSS("'.$href.'");</script>';
    }
  }

  public static function cssPath($css)
  {
    if (in_array($css, self::$css)) {
      return null;
    }
    if (isset(self::$cdn_paths[$css])) {
      $css = self::$cdn_paths[$css];
    }
    if (file_exists('assets/'.$css)) {
      $css = self::$cdn_host.'assets/'.$css;
    }
    if (in_array($css, self::$stylesheet)) {
      return null;
    }

    self::$css[]=$css;
    self::$stylesheet[]=$css;
    return $css;
  }

  /**
  * Adds a script tag of javascript file
  * @param $script Path to js file
  */
  public static function script($script, $uri=false, $prop='')
  {
    if (ob_get_level()===0) {
      if (in_array($script, self::$script)) {
        return;
      }
      self::$script[]=$script;
    }
    if (isset(self::$cdn_paths[$script])) {
      $script = self::$cdn_paths[$script];
    }
    if (file_exists('assets/'.$script)) {
      $script = self::$cdn_host.'assets/'.$script;
    }
    echo '<script src="'.$script.'" '.$prop.'></script>';
  }

  /**
  * Adds a script tag of javascript file lo load asynchronously
  * @param $script Path to js file
  */
  public static function scriptAsync($script, $uri=false)
  {
    if (in_array($script, self::$scriptAsync)) {
      return;
    }
    self::$scriptAsync[]=$script;
    self::script($script, $uri, 'async');
  }

  /**
  * Returns the relative path of the selected theme's folder
  * @return string
  */
  public static function getThemePath()
  {
    return 'themes/'.Config::get('theme');
  }

  public static function getAdminThemePath()
  {
    $e = explode('@', Config::get('admin_theme'));
    $file = 'src/'.($e[1]??'core').'/assets/admin/themes/'.$e[0].'.css';
    return file_exists($file)? $file: null;
  }

  public static function renderAdmin($file, $package = 'core')
  {
    if (Router::request('g_response')==='content') {
      self::renderFile($file, $package);
      return;
    }

    self::includeFile('admin/header.php');
    self::renderFile($file, $package);
    self::includeFile('admin/footer.php');
  }


  public static function render($file, $package = 'core')
  {
    if (Router::request('g_response')==='json') {
      foreach (self::$part as $key => $value) {
        if (is_object($value)) {
          self::$part[$key]=[];
          foreach ($value as $r) {
            self::$part[$key][]=(array)$r;
          }
        }
      }
      echo json_encode(self::$part);
      exit;
    }

    if (self::$onrender || Router::request('g_response')==='content') {
      self::includeFile($file, $package);
      return;
    } else {
      self::includeFile('header.php');
      self::renderFile($file, $package);
      self::includeFile('footer.php');
    }
  }

  public static function head($head = true)
  {
    echo $head?'<head>':'';
    self::includeFile('head.php');
    echo $head?'</head>':'';
  }

  public static function renderFile($filename, $package = 'core')
  {
    $controller = Router::getController();
    $action = Router::getAction();
    if (isset(Config::$onaction[$controller][$action])) {
      foreach (Config::$onaction[$controller][$action] as $fn) {
        $fn();
      }
    }
    if (self::includeFile($filename, $package)===false) {
      http_response_code(404);
      self::includeFile('404.php');
    }
  }

  public static function includeFile($filename, $package='core')
  {
    self::$onrender = true;
    if (substr($filename, -4)=='.php') {
      $filename = substr($filename, 0, -4);
    }
    if (isset(self::$renderer)) {
      $renderer = self::$renderer;
      if ($renderer($filename, $package, self::$part)) {
        return true;
      }
    }

    foreach (self::$part as $key => $value) {
      $$key = $value;
    }

    if ($file = self::getViewFile($filename, $package)) {
      if (in_array($filename, ['header.php','footer.php','header','footer'])) {
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
  public static function getViewFile($file, $package = 'core')
  {
    if (substr($file, -4)!='.php') {
      $file .= '.php';
    }
    if (isset(self::$view_file[$file])) {
      return 'src/'.self::$view_file[$file].'/views/'.$file;
    }
    // DEPRECATED the rest

    $tpath = self::getThemePath().'/'.$file;
    if (file_exists($tpath)) {
      return $tpath;
    }

    if (self::$parent_theme) {
      $tpath = 'themes/'.self::$parent_theme.'/'.$file;
      if (file_exists($tpath)) {
        return $tpath;
      }
    }

    $viewsFolder = self::$view_path[$package]??'src/'.$package.'/views/';
    $spath = $viewsFolder.$file;
    if (file_exists($spath)) {
      return $spath;
    }

    return false;
  }

  /**
  * Overrides a view file. Overrides file from any package or the theme.
  * @param file (string) Relative path of the view file.
  * @param package  (string) The package folder where the file is located.
  */
  public static function setViewFile($file, $package)
  {
    self::$view_file[$file] = $package;
  }

  public static function setViewPath($package, $path)
  {
    self::$view_file[$package] = $path;
  }

  /**
  * Displays a menu
  * @param menu (string) Name of the menu. Default=mainmenu
  * @param tpl  (string) Optional. The view template to generate html
  */
  public static function menu($menu='mainmenu', $tpl='tpl/menu.php')
  {
    $menu_data = Menu::getData($menu);
    $items = Menu::convert($menu_data);
    echo Menu::getHtml($items);
  }

  public static function widget($id, $widget_exp=null)
  {
    global $db,$widget_data;
    if ($res = Widget::getById($id)) {
      $widget_data = json_decode($res[0]->data);
      $type = $res[0]->widget;
    } else {
      "Widget <b>#".$id."</b> is not found";
      return;
    }

    $widget_file = self::getThemePath().'/widgets/'.$type.'.php';

    if (file_exists($widget_file) === false) {
      if (!isset(Config::$widget[$type])) {
        if ($type==='text') {
          $widget_file = "src/core/widgets/text/text.php";
        } else {
          $type = explode('--', $type)[0];
          if (!isset(Config::$widget[$type])) {
            echo "Widget <b>".$type."</b> is not found";
            return;
          }
        }
      }
      @$widget_file = "src/".Config::$widget[$type]."/$type.php";
      if (!file_exists($widget_file)) {
        $type = explode('--', $type)[0];
        @$widget_file = "src/".Config::$widget[$type]."/$type.php";
      }
    }


    $dir = Config::dir(LOG_PATH.'/cache0/widgets/');
    $_file = $dir.$widget_data->widget_id;
    if (file_exists($_file)) {
      include $_file;
    } else {
      ob_start();
      @include $widget_file;
      $out2 = ob_get_contents();
      //ob_end_clean();
      $clog = new Logger(LOG_PATH.'/cache.error.log');
      if (!file_put_contents($_file, $out2)) {
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
  public static function widgetBody($type, $widget_data=null, $widget_file=null)
  {
    if ($widget_file != null) {
      $widget_file = self::getThemePath().'/widgets/'.$widget_file.'.php';
    } else {
      $widget_file = self::getThemePath().'/widgets/'.$type.'.php';
    }
    if (file_exists($widget_file) === false) {
      @$widget_file = "src/".Config::$widget[$type]."/$type.php";
      if (!file_exists($widget_file)) {
        $type = explode('--', $type)[0];
        @$widget_file = "src/".Config::$widget[$type]."/$type.php";
      }
    }
    if (is_object($widget_data)) {
      $data = (array)$widget_data;
    } else {
      $data = &$widget_data;
    }
    @include $widget_file;
  }

  public static function getWidgetBody($type, $widget_data=null, $widget_file=null)
  {
    ob_start();
    self::widgetBody($type, $widget_data, $widget_file);
    return ob_get_clean();
  }

  public static function block($path, $widget_data)
  {
    $block_file = "src/$path/text.php";
    @include $block_file;
  }

  public static function blocks(&$blocks, $prefixId, $anchors=false)
  {
    $html = "";
    foreach ($blocks as $key=>$b) {
      if (!is_object($b)) {
        $b = (object)$b;
      }
      if ($anchors) {
        $html .= "<div id='w$key' class='block-head' data-pos='$key' data-type='{$b->_type}'>";
      }
      $b->widget_id = $prefixId.'_'.$key;
      $html .= self::getWidgetBody($b->_type, $b);
      if ($anchors) {
        $html .= "</div>";
      }
    }
    $key = count($blocks);
    $html .= "<span class='block-end' data-pos='$key'></span>";
    return $html;
  }

  /**
  * Displays the widgets of an area
  * @param $area (string) Area name
  * @param $div (optional boolean) If true, widget body will be printed as child of <div class="widget"> item.
  */
  public static function widgetArea($area, $div=true, $type=null, $widget_file=null)
  {
    $widgets = Widget::getActiveByArea($area);
    if ($widgets) {
      foreach ($widgets as $widget) {
        if ($type != null) {
          if ($widget['widget'] != $type) {
            continue;
          }
        }

        $widget_id = json_decode($widget['id']);
        $widget_data = json_decode($widget['data'], true);
        $widget_data['widget_id'] = $widget_id;

        if ($div) {
          echo '<div class="widget widget-'.$widget['widget'].'" data-id="'.$widget_id.'">';
          if ($widget['title']!='') {
            echo '<div class="widget-title">'.__($widget['title']).'</div>';
          }
          echo '<div class="widget-body">';
        }

        self::widgetBody($widget['widget'], $widget_data);
        if ($div) {
          echo '</div></div>';
        }
      }
    }
    Event::fire($area);
  }

  public static function getWidgetArea($area, $cache=null)
  {
    if ($cache) {
      return Cache::remember('widgetArea.'.$area, $cache, function ($u) {
        return View::getWidgetArea($area);
      }, [Config::mt('widget'), $area]);
    }
    ob_start();
    self::widgetArea($area);
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
  }

  public static function img($src, $max=180, $alt='')
  {
    return '<img src="'.htmlentities(self::thumb($src, $max)).'" alt="'.htmlentities($alt).'">';
  }

  public static function imgLazy($src, $max=180, $alt='')
  {
    return '<img class="lazy" data-src="'.htmlentities(self::thumb($src, $max)).'" alt="'.htmlentities($alt).'">';
  }

  public static function thumb($src, $prefix='', $max=180)
  {
    if (empty($src)) {
      return false;
    }

    $pathinfo = pathinfo($src);
    $ext = $pathinfo['extension'] ?? null;
    if (Config::get('use_webp') && $ext!=='svg') {
      if (strpos($_SERVER['HTTP_ACCEPT']??'', 'image/webp')!==false) {
        $ext = 'webp';
        $type = IMG_WEBP;
      }
    }

    if (is_numeric($prefix)) {
      $prefix .= '/';
      $max = (int)$prefix;
    }

    $file = self::getThumbName($src, $max, $ext, $prefix);
    if ($file===false) {
      return false;
    }
    if ($file===$src) {
      return $src;
    }

    $max_width = $max;
    $max_height = $max;
    if (!file_exists($file)) {
      Image::makeThumb($src, $file, $max_width, $max_height, $type??null);
    }
    Event::fire('View::thumb', [$src,$file]);
    return self::$cdn_host.$file;
  }

  public static function getThumbName($src, $max, $ext = null, $prefix = '')
  {
    $pathinfo = pathinfo($src);
    if ($ext===null) {
      $ext = $pathinfo['extension'] ?? null;
    }
    if ($ext===null || strpos($src, '?')!==false || in_array($ext, ['webm'])) {
      return $src;
    }
    if ((strpos($src, 'assets/')===0 || strpos($src, 'src/')===0)
      && in_array($ext, ['svg'])) {
      return $src;
    }

    if ($src[0]==='$') {
      if (substr($src, 1, 2)=='p=') {
        $file = 'assets/themes/'.Config::get('theme').'/'.substr($src, 3);
        if (!file_exists($file)) {
          $file='assets/core/photo.png';
        }
        return $file;
      }
    }
    $ext = strtolower($ext);
    $file = null;
    $thumbs = [];
    $key = $pathinfo['filename'].$ext.$max;
    $thumbsjson = $pathinfo['dirname'].'/.thumbs.json';

    if (strpos($src, SITE_PATH.'data/') !== 0) {
      FileManager::$sitepath = realpath(SITE_PATH);
      if (strpos($src, 'src/') === 0  || strpos($src, 'themes/') === 0 ||
        !FileManager::allowedPath($src, true)) {
        return $src;
      }
      return TMP_PATH.'/'.$prefix.Slugify::text($pathinfo['dirname'].$pathinfo['filename']).'.'.$ext;
    }


    if (file_exists($thumbsjson)) {
      $thumbs = json_decode(file_get_contents($thumbsjson), true);
      $file = $thumbs[$key] ?? null;
    }
    if (!$file) {
      if (Image::imageExtention($ext)===false && $ext!='svg') {
        return false;
      }
      do {
        $basename = substr(bin2hex(random_bytes(30)), 0, 30);
        $file = TMP_PATH.'/'.$basename.'-'.$max.'.'.$ext;
        $thumbs[$key] = $file;
      } while (strlen($basename) < 30 || file_exists($file));
      file_put_contents($thumbsjson, json_encode($thumbs));
    }
    return $file;
  }

  public static function thumbStack($src_array, $file, $max=180)
  {
    $max_width = $max;
    $max_height = $max;
    if (!file_exists($file) || !file_exists($file.'.json')) {
      return Image::makeStack(1, $src_array, $file, $max_width, $max_height);
    }
    $stack = json_decode(file_get_contents($file.'.json'), true);
    if (!is_array($stack[1])) {
      $stack[1] = [];
    }
    if (is_nan($stack[0])) {
      $stack[0] = 0;
    }

    foreach ($src_array as $key=>$value) {
      $key_src = $stack[1][$key]['src'];
      if ($key_src != $value && pathinfo($key_src)['extension']=='jpg') {
        return Image::makeStack($stack[0]+1, $src_array, $file, $max_width, $max_height);
      }
    }
    Event::fire('View::thumbStack', [$src_array,$file]);
    return [$file.'?'.$stack[0], $stack[1]];
  }

  public static function getTemplates($template)
  {
    $options = [];

    foreach (self::$view_file as $key => $value) {
      $exploded = explode('--', $key);
      if ($exploded[0] === $template) {
        $options[] = explode('.', $exploded[1])[0];
      }
    }

    $files = glob(self::getThemePath().'/'.$template.'--*');
    foreach ($files as $file) {
      $base = explode('--', $file)[1];
      $options[] = explode('.', $base)[0];
    }

    $files = glob('src/core/views/'.$template.'--*');
    foreach ($files as $file) {
      $base = explode('--', $file)[1];
      $options[] = explode('.', $base)[0];
    }

    return array_unique($options);
  }

  /**
  * $srcset = View::thumbSrcset($src);
  * @example background-image: -webkit-image-set(url({$srcset[0]}) 1x, url({$srcset[1]}) 2x);
  * @example <img srcset="{$srcset[0]}, {$srcset[0]} 2x" src="{$srcset[0]}"
  */
  public static function thumbSrcset($src, $sizes = [1200,600])
  {
    $r = [];
    foreach ($sizes as $w) {
      $r[] = self::thumb($src, $w);
    }
    return $r;
  }
}
