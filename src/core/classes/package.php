<?php


class package {

    function __construct()
    {
        $activate = router::get('activate');
        if($activate) self::activate($activate);
        $deactivate = router::get('deactivate');
        if($deactivate) self::deactivate($deactivate);
        $save_options = router::get('save_options');
        if($activate) self::save_options($save_options);
        $options = router::post('options');
        if($activate) self::options($options);
    }

    static function activate($activate)
    {
        if (in_array($activate,$packages)) {
            if(!in_array($activate, $GLOBALS['config']['packages'])) {
                $GLOBALS['config']['packages'][]=$activate;
                $updatefile = 'src/'.$activate.'/update.php';
                if(file_exists($updatefile)) include $updatefile;
                gila::updateConfigFile();
                usleep(300);
                $alert = gila::alert('success','Package activated');
                exit;
            }
        }
    }

    static function deactivate($deactivate)
    {
        if (in_array($deactivate,$GLOBALS['config']['packages'])) {
            $key = array_search($deactivate, $GLOBALS['config']['packages']);
                unset($GLOBALS['config']['packages'][$key]);
                gila::updateConfigFile();
                usleep(100);
                $alert = gila::alert('success',"Package $key deactivated");
                exit;
        }
    }

    static function download($download)
    {
        if ($download) {
          $zip = new ZipArchive;
          $target = 'src/'.$download;
          $file = 'http://gilacms.com/assets/packages/'.$download.'.zip';
          $localfile = 'src/'.$download.'.zip';
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

    static function options($options)
    {
        if (in_array($options,$GLOBALS['config']['packages'])) {
            global $db;
            echo '<form id="addon_options_form" class="g-form"><input id="addon_id" value="'.$options.'" type="hidden">';
            $pack=$options;
            if(file_exists('src/'.$options.'/package.json')) {
                $pac=json_decode(file_get_contents('src/'.$options.'/package.json'),true);
                $options=$pac['options'];
            } else include 'src/'.$options.'/package.php';

            foreach($options as $key=>$op) {
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
            return;
        }
    }

    static function save_options($save_options)
    {
        if (in_array($save_options,$GLOBALS['config']['packages'])) {
        	global $db;
        	foreach($_POST['option'] as $key=>$value) {
        		$ql="INSERT INTO `option`(`option`,`value`) VALUES('$save_options.$key','$value') ON DUPLICATE KEY UPDATE `value`='$value';";
        		$db->query($ql);
        	}
            return;
        }
    }

    static function scan()
    {
        $dir = "src/";
        $scanned = scandir($dir);
        $packages = [];
        foreach($scanned as $folder) {
            $json = $dir.$folder.'/package.json';
            if(file_exists($json)) {
                $data = json_decode(file_get_contents($json));
                $data->title = $data->name;
                $data->url = isset($data->url)?$data->url:'';
                $packages[$folder] = $data;
            }
        }
        return $packages;
    }

}
