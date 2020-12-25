<?php

namespace Gila;

use DateTime;

class Package
{
  private static $config = [];

  public function __construct()
  {
    ob_end_clean();
    $activate = Router::post('activate');
    if ($activate) {
      self::activate($activate);
    }
    $deactivate = Router::post('deactivate');
    if ($deactivate) {
      self::deactivate($deactivate);
    }
    $save_options = Router::param('save_options');
    if ($save_options) {
      self::saveOptions($save_options);
    }
    $options = Router::post('options');
    if ($options) {
      self::options($options);
    }
    $download = Router::post('download');
    if (Config::get('test')=='1') {
      $download = Router::request('test', $download);
    }
    if ($download && FS_ACCESS) {
      if (self::download($download)===true) {
        if (!$_REQUEST['g_response']) {
          echo '<meta http-equiv="refresh" content="2;url='.Config::base().'/admin/packages" />';
          echo __('_package_downloaded').'. Redirecting...';
          exit;
        } else {
          echo '{"success":true}';
        }
      } else {
        echo '{"success":false}';
      }
    }
    $html = Router::post('html');
    if ($html) {
      self::display($html);
    }
  }

  public static function config($package)
  {
    if (!isset(self::$config[$package])) {
      self::$config[$package] = json_decode(file_get_contents('src/'.$package.'/package.json'), true);
    }
    return self::$config[$package];
  }

  public static function version($package)
  {
    return self::config($package)['version'];
  }

  public static function display($package)
  {
    $itemName = 'package-html';
    echo Cache::remember($itemName, 259200, function ($u) {
      return file_get_contents("https://gilacms.com/addons/package/{$u[0]}/html");
    }, [$package, self::config($package)['version']??'']);
  }

  /**
  * Activates a package
  * @param $activate (string) Package name to activate
  */
  public static function activate($activate)
  {
    $packages = Config::packages();
    if (in_array($activate, scandir('src/'))) {
      if (!in_array($activate, $packages) ||
         Config::get('env')=='dev') {
        $pac=json_decode(file_get_contents('src/'.$activate.'/package.json'), true);
        $require = [];
        $require_op = [];
        if (isset($pac['require'])) {
          foreach ($pac['require'] as $key => $value) {
            if (!in_array($key, $packages) && $key!='core') {
              if (!file_exists('vendor/'.$key)) {
                $require[$key]=$value;
              }
            } else {
              $pacx = self::config($key);
              if (version_compare($pacx['version'], $value) < 0) {
                $require[$key]=$value;
              }
            }
          }
        }
        if (isset($pac['options'])) {
          foreach ($pac['options'] as $key=>$option) {
            if (@$option['required']==true) {
              if (Config::get($activate.'.'.$key)===null) {
                $require_op[] = $option['title'] ?? $key;
              }
            }
          }
        }

        if ($require===[] && $require_op===[]) {
          if (!in_array($activate, $packages) && $activate!=='core') {
            $packages[]=$activate;
          }
          self::copyAssets($activate);
          self::update($activate);
          Config::set('packages', $packages);
          self::updateLoadFile();
        } else {
          if ($require!=[]) {
            $error = __('_packages_required').':';
            foreach ($require as $k=>$r) {
              if (strpos($k, '/')) {
                $error .= "<br><a href='https://gilacms.com/blog/36' target='_blank'>$k $r</a>";
              } else {
                $error .= "<br><a href='admin/packages/new?search=$k' target='_blank'>$k v$r</a>";
              }
            }
          }
          if ($require_op!=[]) {
            $error = '<br>'.__('_options_required').': '.implode(', ', $require_op);
          }
        }
      } else {
        $error = __("_package_activated");
      }
    } else {
      $error = __("_package_not_downloaded");
    }

    if (isset($error)) {
      echo '{"success":false,"error":"'.$error.'"}';
    } else {
      echo '{"success":true}';
    }
  }

  /**
  * Deactivates a package and its dependecies
  * @param $deactivate (string) Package name to deactivate
  */
  public static function deactivate($deactivate)
  {
    $packages = Config::packages();
    if (in_array($deactivate, $packages)) {
      $key = array_search($deactivate, $packages);
      unset($packages[$key]);

      // deactivate other packages that require $deactivate
      foreach (Config::packages() as $p) {
        $string = file_get_contents("/src/$p/package.json");
        $json_p = json_decode($string, true);
        if (isset($json_p['require'])) {
          if (isset($json_p['require'][$deactivate])) {
            $key = array_search($deactivate, $packages);
            if ($key !== false) {
              unset($packages[$key]);
            }
          }
        }
      }
      Config::set('packages', $packages);
      self::updateLoadFile();
      echo '{"success":true}';
    }
  }

  /**
  * Downloads a package from gilacms.com assets in zip
  * @param $package (string) Package name to download
  */
  public static function download($package)
  {
    if (!$package) {
      return false;
    }
    $zip = new \ZipArchive;
    $target = 'src/'.$package;
    $request = 'https://gilacms.com/packages/?package='.$package;
    $request .= Config::get('test')=='1' && isset($_GET['test']) ? '&test=1' : '';
    $pinfo = json_decode(file_get_contents($request), true)[0];

    if (!$pinfo) {
      return false;
    }
    $file = 'https://gilacms.com/assets/packages/'.$package.'.zip';
    if (substr($pinfo['download_url'], 0, 8)==='https://') {
      $file = $pinfo['download_url'];
    }

    $tmp_name = $target.'__tmp__';
    $localfile = 'src/'.$package.'.zip';

    if (!copy($file, $localfile)) {
      return false;
    }
    if ($zip->open($localfile) === true) {
      $previousFolder = LOG_PATH.'/previous-packages/';
      $month_in_seconds = 2592000;
      $zip->extractTo($tmp_name);
      $zip->close();
      if (file_exists($target)) {
        $res = rename($target, Config::dir($previousFolder.date("Y-m-d H:i:s").' '.$package));
        if ($res===false) {
          return false;
        }
      }
      $previousPackages = scandir($previousFolder);
      foreach ($previousPackages as $folder) {
        if (filemtime($previousFolder.$folder) < time()-$month_in_seconds) {
          FileManager::delete($previousFolder.$folder);
        }
      }

      $unzipped = scandir($tmp_name);
      if (count(scandir($tmp_name))==3) {
        if ($unzipped[2][0]!='.') {
          $tmp_name .= '/'.$unzipped[2];
        }
      }
      $res = rename($tmp_name, $target);
      if ($res===false) {
        return false;
      }
      if (file_exists($target.'__tmp__')) {
        rmdir($target.'__tmp__');
      }
      self::updateAfterDownload($package);
      unlink(LOG_PATH.'/load.php');
      unlink($localfile);
      return true;
    }
    return false;
  }

  public static function updateAfterDownload($package)
  {
    global $db;
    self::copyAssets($package);
    self::update($package);

    $sites = scandir('sites');
    foreach ($sites as $site) {
      if ($site[0]!='.') {
        $config = 'sites/'.$site.'/config.php';
        if (file_exists($config)) {
          include $config;
          $db = new Db($GLOBALS['config']['db']);
          if ($package==='core' ||
            in_array($package, Config::packages())) {
            self::update($package);
          }
          @unlink('sites/'.$site.'/log/load.php');
        }
      }
    }
  }

  public static function update($package)
  {
    $update_file = 'src/'.$package.'/update.php';
    if (file_exists($update_file)) {
      include $update_file;
    }
  }

  public static function copyAssets($package)
  {
    $assets = 'src/'.$package.'/assets';
    if (file_exists($assets)) {
      FileManager::copy($assets, 'assets/'.$package);
    }
  }

  /**
  * Returns the package options on html
  * @param $package (string) Package name to generate the options code
  */
  public static function options($package)
  {
    if (file_exists('src/'.$package)) {
      global $db;
      echo '<form id="addon_options_form" class="g-form">';
      echo '<input id="addon_id" value="'.$package.'" type="hidden">';
      $pack=$package;
      if (file_exists('src/'.$package.'/package.json')) {
        $pac=json_decode(file_get_contents('src/'.$package.'/package.json'), true);
        @$options=$pac['options'];
      } else {
        die('Could not find src/'.$package.'/package.json');
      }

      if (is_array($options)) {
        foreach ($options as $key=>$op) {
          $values[$key] = Config::option($pack.'.'.$key);
        }
        echo Form::html($options, $values, 'option[', ']');
      } // else error alert
      echo "</form>";
    }
  }

  /**
  * Saves option values for a package
  * @param $package (string) Package name
  */
  public static function saveOptions($package)
  {
    global $db;
    $jsonFile = 'src/'.$package.'/package.json';
    if (file_exists($jsonFile)) {
      $data = json_decode(file_get_contents($jsonFile), true);
      foreach ($_POST['option'] as $key=>$value) {
        if (isset($data['options'][$key])) {
          $allowed = $data['options'][$key]['allow_tags'] ?? false;
          $value = HtmlInput::purify($value, $allowed);
          $ql="INSERT INTO `option`(`option`,`value`) VALUES(?,?) ON DUPLICATE KEY UPDATE `value`=?;";
          $db->query($ql, [$package.'.'.$key, $value, $value]);
        }
      }
      if (Config::get('env')==='pro') {
        unlink(LOG_PATH.'/load.php');
      }
    }
  }

  /**
  * Returns the installed packages in an array option values for a package
  * @return Array Packages
  */
  public static function scan()
  {
    if (!FS_ACCESS && Config::get('available_packages')) {
      return Config::get('available_packages');
    }
    $dir = "src/";
    $scanned = scandir($dir);
    $_packages = [];
    foreach ($scanned as $folder) {
      if ($folder[0] != '.') {
        $json = $dir.$folder.'/package.json';
        if (file_exists($json)) {
          $data = json_decode(file_get_contents($json));
          @$data->title = @$data->title?? @$data->name;
          $data->package = $folder;
          $data->url = @$data->homepage?? (@$data->url?? '');
          $_packages[$folder] = $data;
        }
      }
    }
    return $_packages;
  }

  /**
  * Combines all package load.php files in log/load.php
  * @return Array Packages
  */
  public static function updateLoadFile()
  {
    global $db;
    $file = LOG_PATH.'/load.php';
    $contents = '<?php';
    $packages = Config::packages();
    if (!in_array('core', $packages)) {
      $packages = array_merge(['core'], $packages);
    }
    foreach ($packages as $package) {
      $handle = @fopen("src/$package/load.php", "r");
      if ($handle) {
        $line = fgets($handle);
        $contents .= "\n\n/*--- $package ---*/";
        while (($line = fgets($handle)) !== false) {
          $contents .= $line;
        }

        fclose($handle);
      } else {
        // error op
      }
    }
    Config::$option=[];
    $db->connect();
    $res = $db->get('SELECT `option`,`value` FROM `option`;');
    foreach ($res as $r) {
      Config::$option[$r[0]] = $r[1];
    }
    $db->close();

    $contents .= "\n\nConfig::\$option = ".var_export(Config::$option, true).";\n";

    file_put_contents($file, $contents);
  }

  public static function check4updates()
  {
    if (Config::get('check4updates')==0) {
      return;
    }
    $now = new DateTime("now");
    if (Config::get('checked4updates')===null) {
      Config::set('checked4updates', $now->format('Y-m-d'));
      $diff = 1000;
    } else {
      $diff = date_diff(new DateTime(Config::get('checked4updates')), new DateTime("now"))->format('%a');
    }

    // check once a day
    if ($diff>1) {
      $installed_packages = self::scan();
      $packages2update = [];
      $versions = [];
      $url = "https://gilacms.com/addons/package_versions?p[]=".implode('&p[]=', array_keys($installed_packages));
      $url .= Config::get('test')=='1' ? '&test=1' : '';
      if ($res = file_get_contents($url)) {
        Config::set('checked4updates', $now->format('Y-m-d H:i:s'));
        $versions = json_decode($res, true);
      }
      foreach ($installed_packages as $ipac=>$pac) {
        if (isset($versions[$ipac]) && version_compare($versions[$ipac], $pac->version) == 1) {
          if ($pac==='core' && Config::get('autoupdate')==1) {
            self::updateAfterDownload($pac);
            View::alert('info', 'System updated successfully to v'.$versions[$ipac]);
          } else {
            $packages2update[$ipac] = $versions[$ipac];
          }
        }
      }
      if ($packages2update != []) {
        file_put_contents(LOG_PATH.'/packages2update.json', json_encode($packages2update, JSON_PRETTY_PRINT));
      }
    }

    if (file_exists(LOG_PATH.'/packages2update.json')) {
      return true;
    }
    return false;
  }
}
