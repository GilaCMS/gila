<?php

class Theme
{

  function __construct()
  {
    $activate = router::post('activate');
    if($activate) self::activate($activate);
    $download = router::post('download');
    if($download) self::download($download);
    $save_options = router::get('save_options');
    if($save_options) self::save_options($save_options);
    $options = router::post('options');
    if($options) self::options($options);
  }

  /**
  * Activates a theme
  * @param $activate (string) Theme name to activate
  */
  static function activate($activate)
  {
    if (in_array($activate, scandir('themes/'))) {
      if($activate != $GLOBALS['config']['theme']) {
        $pac=json_decode(file_get_contents('src/'.$activate.'/package.json'),true);
        $require = [];
        if(isset($pac['require'])) foreach ($pac['require'] as $key => $value) {
          if(!in_array($key, gila::packages())&&($key!='core'))
            $require[$key]=$key.' v'.$value;
          else {
            $pacx=json_decode(file_get_contents('src/'.$key.'/package.json'),true);
            if(version_compare($pacx['version'], $value) < 0) $require[$key]=$key.' v'.$value;
          }
        }

        if($require==[]) {
          $GLOBALS['config']['theme']=$activate;
          gila::updateConfigFile();
          package::updateLoadFile();
          usleep(300);
          view::alert('success', __('_theme_selected'));
          echo 'ok';
        }
        else {
          echo __('_packages_required').':';
          foreach($require as $k=>$r) echo "<br><a href='admin/addons/search/$k'>$r</a>";
        }
      } else echo __("_theme_selected");
    } else echo __("_theme_not_downloaded");
    exit;
  }

  /**
  * Downloads a theme from gilacms.com assets in zip
  * @param $download (string) Theme name to download
  */
  static function download($download)
  {
    if ($download) {
      $zip = new ZipArchive;
      $target = 'themes/'.$download;
      $request = 'https://gilacms.com/packages/themes?theme='.$download;
      $pinfo = json_decode(file_get_contents($request), true)[0];

      if(!$pinfo) {
        echo __('_theme_not_downloaded');
        exit;
      }
      $file = 'https://gilacms.com/assets/themes/'.$download.'.zip';
      if(substr($pinfo['download_url'],0,8)=='https://'){
        $file = $pinfo['download_url'];
      }
      if(isset($_GET['src'])) $file = $_GET['src'];
      $tmp_name = $target.'__tmp__';
      $localfile = 'themes/'.$download.'.zip';

      if (!copy($file, $localfile)) {
        echo __('_theme_not_downloaded');
        exit;
      }
      if ($zip->open($localfile) === true) {
        if(!file_exists($target)) mkdir($target);
        $zip->extractTo($tmp_name);
        $zip->close();
        if(file_exists($target)) {
        rename($target, gila::dir(LOG_PATH.'/previous-themes/'.date("Y-m-d H:i:s").' '.$download));
        }
        $unzipped = scandir($tmp_name);
        if(count(scandir($tmp_name))==3) if($unzipped[2][0]!='.') $tmp_name .= '/'.$unzipped[2];
        rename($tmp_name, $target);
        if(file_exists($target.'__tmp__')) rmdir($target.'__tmp__');

        unlink(LOG_PATH.'/load.php');
        unlink($localfile);
        echo 'ok';
        if(!$_REQUEST['g_response']) {
          echo '<meta http-equiv="refresh" content="2;url='.gila::base_url().'/admin/themes" />';
        }
      } else {
        echo __('_theme_not_downloaded');
      }
      exit;
    }
  }

  /**
  * Returns the theme options in html
  * @param $options (string) Theme name to generate the options code
  */
  static function options($options)
  {
    if (file_exists('themes/'.$options)) {
      echo '<form id="theme_options_form" class="g-form"><input id="theme_id" value="'.$options.'" type="hidden">';
      $pack = $options;
      if(file_exists('themes/'.$options.'/package.json')) {
        $pac=json_decode(file_get_contents('themes/'.$options.'/package.json'),true);
        $options=$pac['options'];
      } else include 'themes/'.$options.'/package.php';

      if(is_array($options)) {
        foreach($options as $key=>$op) {
          $values[$key] = gila::option('theme.'.$key);
        }
        echo gForm::html($options,$values,'option[',']');
      }// else error alert
      echo "</form>";
      exit;
    }
    exit;
  }

  /**
  * Saves option values for a theme
  * @param $theme (string) Theme name
  */
  static function save_options($theme)
  {
    global $db;
    $jsonFile = 'themes/'.$theme.'/package.json';
    if (file_exists($jsonFile)) {
      $data = json_decode(file_get_contents($jsonFile),true);
      foreach($_POST['option'] as $key=>$value) if(isset($data['options'][$key])){
        if(!isset($data['options'][$key]['allow-tags'])
            || $data['options'][$key]['allow-tags']===false) {
          $value=strip_tags($value);
        }
        $ql="INSERT INTO `option`(`option`,`value`) VALUES('theme.$key',?) ON DUPLICATE KEY UPDATE `value`=?;";
        $db->query($ql, [$value,$value]);
      }
      if(gila::config('env')=='pro') unlink(LOG_PATH.'/load.php');
      exit;
    }
  }

  /**
  * Returns the installed themes in an array
  * @return Array Themes
  */
  static function scan()
  {
    $dir = "themes/";
    $scanned = scandir($dir);
    $_packages = [];
    foreach($scanned as $folder) if($folder[0] != '.'){
      $json = $dir.$folder.'/package.json';
      if(file_exists($json)) {
        $data = json_decode(file_get_contents($json));
        @$data->title = @$data->title?? @$data->name;
        $data->package = $folder;
        $data->url = @$data->homepage?? (@$data->url?? '');
        $_packages[$folder] = $data;
      }
    }
    return $_packages;
  }

}
