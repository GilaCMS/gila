<?php

class theme {

    function __construct()
    {
        $activate = router::get('activate');
        if($activate) self::activate($activate);
        $download = router::get('download');
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
                    view::alert('success','Theme selected');
                    echo 'ok';
                }
                else {
                    echo "These packages must be activated:";
                    foreach($require as $k=>$r) echo "<br><a href='admin/addons/search/$k'>$r</a>";
                }
            } else echo "Theme is already selected";
        } else echo "Theme is not downloaded";
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
            $file = 'http://gilacms.com/assets/themes/'.$download.'.zip';
            $localfile = 'themes/'.$download.'.zip';
            if (!copy($file, $localfile)) {
              echo "Failed to download theme!";
            }
            if ($zip->open($localfile) === TRUE) {
              if(!file_exists($target)) mkdir($target);
              $zip->extractTo($target);
              $zip->close();
              echo 'ok';
            } else {
              echo 'Failed to download theme!';
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
        if ($options == gila::config('theme')) {
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
                include view::getViewFile('admin/optionInputs.php');
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
        if ($theme == gila::config('theme')) {
        	global $db;
            foreach($_POST['option'] as $key=>$value) {
        		$ql="INSERT INTO `option`(`option`,`value`) VALUES('theme.$key','$value') ON DUPLICATE KEY UPDATE `value`='$value';";
        		$db->query($ql);
        	}
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
                @$data->title = @$data->name;
                $data->package = $folder;
                $data->url = isset($data->url)?$data->url:'';
                $_packages[$folder] = $data;
            }
        }
        return $_packages;
    }

}
