
<?php
$dir = "src/";
$packages = scandir($dir);
//$table = '<tr><th class="col-xs-8 gm-8"><th class="col-xs-2 gm-2">';
$table = '';
$pn = 0; $alert = '';

$activate = router::get('activate');
if (in_array($activate,$packages)) {
    if(!in_array($activate, $GLOBALS['config']['packages'])) {
        $GLOBALS['config']['packages'][]=$activate;
        gila::updateConfigFile();
        usleep(100);
        $alert = gila::alert('success','Package activated');
        exit;
    }
}

$deactivate = router::get('deactivate');
if (in_array($deactivate,$GLOBALS['config']['packages'])) {
    $key = array_search($deactivate, $GLOBALS['config']['packages']);
        unset($GLOBALS['config']['packages'][$key]);
        gila::updateConfigFile();
        usleep(100);
        $alert = gila::alert('success',"Package $key deactivated");
        exit;
}

$options = router::post('options');
if (in_array($options,$GLOBALS['config']['packages'])) {
    echo '<form id="addon_options_form"><input id="addon_id" value="'.$options.'" type="hidden">';
    $pack=$options;
    include __DIR__.'/../../../'.$options.'/package.php';
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
        } else echo '<input class="g-input gm-8" name="option['.$key.']" value="'.$ov.'">';
        echo '</div><br>';
    }
    echo "</form>";
    return;
}


$save_options = router::get('save_options');
if (in_array($save_options,$GLOBALS['config']['packages'])) {
	global $db;
	foreach($_POST['option'] as $key=>$value) {
		$ql="INSERT INTO `option`(`option`,`value`) VALUES('$save_options.$key','$value') ON DUPLICATE KEY UPDATE `value`='$value';";
		echo $ql;
		$db->query($ql);
	}
    return;
}


foreach ($packages as $p) if($p[0] != '.') if(file_exists($dir."$p/package.php")){
    include $dir."$p/package.php";
    $table .= '<tr>';
    /*if (file_exists($dir."$p/logo.png")) {
        $table .= '<td><div><img src="'."src/$p/logo.png".'" style="width:100%" /></div>';
    }
    else {
        $table .= '<td style="background:#999; align:middle"><span>'.($name?:$p).'</span>';
    }*/

    $table .= '<td style="width:100%"><h4>'.($name?:$p).' '.($version?:'');
    $table .= '</h4>'.(isset($description)?$description:'No description');
    $table .= '<br><b>Author:</b> '.(isset($author)?$author:'');
    $table .= (isset($url)?' <b>Url:</b> <a href="'.$url.'" target="_blank">'.$url.'</a>':'');
    $table .= (isset($contact)?' <b>Contact:</b> '.$contact:'');

    if (in_array($p,$GLOBALS['config']['packages'])) {
        //if (new_version) $table .= 'Upgrade<br>';
        $table .= "<td><a onclick='addon_options(\"{$p}\")' class='g-btn' style='display:inline-flex'><i class='fa fa-gears'></i>&nbsp;Options</a><td>";
        $table .= "<a onclick='addon_deactivate(\"{$p}\")' class='g-btn error'>Deactivate</a>";
    }
    else {
        $table .= "<td><td><a onclick='addon_activate(\"{$p}\")' class='g-btn success'>Activate</a>";
    }
    $pn++;
}
?>
<?=$alert?>
<table class='g-table'><?=$table?></table>

<script>
function addon_activate(p){ g.ajax('admin/addons?g_response=content&activate='+p,function(x){
    g.alert('Package successfully activated!','success','location.reload(true)');
})};
function addon_deactivate(p){ g.ajax('admin/addons?g_response=content&deactivate='+p,function(x){
    g.alert('Package deactivated!','notice','location.reload(true)');
})};

g.dialog.buttons.save_options = {
    title:'Save Options',fn:function(){
		let p = g.el('addon_id').value;
		let fm=new FormData(g.el('addon_options_form'))
        g.ajax({url:'admin/addons?g_response=content&save_options='+p,method:'POST',data:fm,fn:function(x){
			g('#gila-darkscreen').remove();
		}})
    }
}

function addon_options(p) {
 g.post("admin/addons",'g_response=content&options='+p,function(x){
     g.dialog({title:"Options",body:x,buttons:'save_options'})
 })
}

</script>
