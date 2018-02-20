<?php


class package {

    function __construct()
    {
        $activate = router::get('activate');
        if($activate) self::activate($activate);
        $deactivate = router::get('deactivate');
        if($deactivate) self::deactivate($deactivate);
        $download = router::get('download');
        if($download) self::download($download);
        $save_options = router::get('save_options');
        if($save_options) self::save_options($save_options);
        $options = router::post('options');
        if($options) self::options($options);
    }

    /**
    * Activates a package
    * @param $activate (string) Package name to activate
    */
    static function activate($activate)
    {
        if (in_array($activate, scandir('src/'))) {
            if(!in_array($activate, $GLOBALS['config']['packages'])) {
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
                    $GLOBALS['config']['packages'][]=$activate;
                    $updatefile = 'src/'.$activate.'/update.php';
                    if(file_exists($updatefile)) include $updatefile;
                    gila::updateConfigFile();
                    usleep(300);
                    view::alert('success','Package activated');
                    echo 'ok';
                }
                else {
                    echo "These packages must be activated:";
                    foreach($require as $k=>$r) echo "<br><a href='admin/addons/search/$k'>$r</a>";
                }
            } else echo "Package is already active";
        } else echo "Package is not downloaded";
        exit;
    }

    /**
    * Deactivates a package and its dependecies
    * @param $deactivate (string) Package name to deactivate
    */
    static function deactivate($deactivate)
    {
        if (in_array($deactivate,$GLOBALS['config']['packages'])) {
            $key = array_search($deactivate, $GLOBALS['config']['packages']);
            unset($GLOBALS['config']['packages'][$key]);

            // deactivate other packages that require $deactivate
            foreach($GLOBALS['config']['packages'] as $p) {
                $string = file_get_contents("/src/$p/package.json");
                $json_p = json_decode($string, true);
                if(isset($json_p['require'])) if(isset($json_p['require'][$deactivate])) {
                    $key = array_search($deactivate, $GLOBALS['config']['packages']);
                    if($key !== false) unset($GLOBALS['config']['packages'][$key]);
                }
            }
            gila::updateConfigFile();
            usleep(100);
            $alert = gila::alert('success',"Package $key deactivated");
            exit;
        }
        exit;
    }

    /**
    * Downloads a package from gilacms.com assets in zip
    * @param $package (string) Package name to download
    */
    static function download($package)
    {
        if ($package) {
          $zip = new ZipArchive;
          $target = 'src/'.$package;
          $file = 'http://gilacms.com/assets/packages/'.$package.'.zip';
          $localfile = 'src/'.$package.'.zip';
          if (!copy($file, $localfile)) {
            echo "Failed to download package!";
          }
          if ($zip->open($localfile) === TRUE) {
            if(!file_exists($target)) mkdir($target);
            $zip->extractTo($target);
            $zip->close();
            if(file_exists('src/core/update.php')) include 'src/core/update.php';
            echo 'ok';
          } else {
            echo 'Failed to download package!';
          }
          exit;
        }
    }

    /**
    * Returns the package options on html
    * @param $package (string) Package name to generate the options code
    */
    static function options($package)
    {
        if (in_array($package,$GLOBALS['config']['packages'])) {
            global $db;
            echo '<form id="addon_options_form" class="g-form"><input id="addon_id" value="'.$options.'" type="hidden">';
            $pack=$package;
            if(file_exists('src/'.$package.'/package.json')) {
                $pac=json_decode(file_get_contents('src/'.$package.'/package.json'),true);
                @$options=$pac['options'];
            } else die('Could not find src/'.$package.'/package.json');

            if(is_array($options)) foreach($options as $key=>$op) {
                echo '<div class="gm-12">';
                echo '<label class="gm-4">'.(isset($op['title'])?$op['title']:ucwords($key)).'</label>';
                $ov = gila::option($pack.'.'.$key);
                if(isset($op['type'])) {
                    if($op['type']=='select') {
                        if(!isset($op['options'])) die("<b>Option $key require options</b>");
                        echo '<select class="g-input gm-8" name="option['.$key.']">';
                        foreach($op['options'] as $value=>$name) {
                            echo '<option value="'.$value.'"'.($value==$ov?' selected':'').'>'.$name.'</option>';
                        }
                        echo '</select>';
                    }
                    if($op['type']=='postcategory') {
                        echo '<select class="g-input gm-8" name="option['.$key.']">';
                        $res=$db->get('SELECT id,title FROM postcategory;');
                        echo '<option value=""'.(''==$ov?' selected':'').'>'.'[All]'.'</option>';
                        foreach($res as $r) {
                            echo '<option value="'.$r[0].'"'.($r[0]==$ov?' selected':'').'>'.$r[1].'</option>';
                        }
                        echo '</select>';
                    }
                } else echo '<input class="g-input gm-8" name="option['.$key.']" value="'.$ov.'">';
                echo '</div><br>';
            }
            echo "</form>";
            exit;
        }
        exit;
    }

    /**
    * Saves option values for a package
    * @param $package (string) Package name
    */
    static function save_options($package)
    {
        if (in_array($package,$GLOBALS['config']['packages'])) {
        	global $db;
        	foreach($_POST['option'] as $key=>$value) {
        		$ql="INSERT INTO `option`(`option`,`value`) VALUES('$package.$key','$value') ON DUPLICATE KEY UPDATE `value`='$value';";
        		$db->query($ql);
        	}
            exit;
        }
    }

    /**
    * Returns the installed packages in an array option values for a package
    * @return Array Packages
    */
    static function scan()
    {
        $dir = "src/";
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
