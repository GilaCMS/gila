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
        $updatefile = 'src/'.$activate.'/update.php';
        if(file_exists($updatefile)) include $updatefile;
        gila::updateConfigFile();
        usleep(300);
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

$download = router::get('download');
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

$options = router::post('options');
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


$save_options = router::get('save_options');
if (in_array($save_options,$GLOBALS['config']['packages'])) {
	global $db;
	foreach($_POST['option'] as $key=>$value) {
		$ql="INSERT INTO `option`(`option`,`value`) VALUES('$save_options.$key','$value') ON DUPLICATE KEY UPDATE `value`='$value';";
		$db->query($ql);
	}
    return;
}


foreach ($packages as $p) if($p[0] != '.') if(file_exists($dir."$p/package.php") || file_exists($dir."$p/package.json")) {
    $pac=[];
    if($p=='core') continue;
    if(file_exists($dir."$p/package.json")) {
        $pac=json_decode(file_get_contents($dir."$p/package.json"));
        $table .= '<tr>';
        $table .= '<td style="color:grey;text-align:center">';
        if (file_exists($dir."$p/logo.png")) {
            $table .= '<img class="fa fa-3x" src="'."src/$p/logo.png".'" />';
        }
        else {
            $table .= '<i class="fa fa-3x fa-dropbox"></i>';
        }
        $table .= '<td style="width:100%"><h4>'.(isset($pac->name)?$pac->name:$p).' '.(isset($pac->version)?$pac->version:'');
        $table .= '</h4>'.(isset($pac->description)?$pac->description:'No description');
        $table .= '<br><b>Author:</b> '.(isset($pac->author)?$pac->author:'');
        $table .= (isset($pac->url)?' <b>Url:</b> <a href="'.$pac->url.'" target="_blank">'.$pac->url.'</a>':'');
        $table .= (isset($pac->contact)?' <b>Contact:</b> '.$pac->contact:'');
        unset($options);
    }else{
        include $dir."$p/package.php";
        $table .= '<tr>';
        $table .= '<td style="color:grey;text-align:center"><i class="fa fa-3x fa-download"></i>';
        $table .= '<td style="width:100%"><h4>'.($name?:$p).' '.($version?:'');
        $table .= '</h4>'.(isset($description)?$description:'No description');
        $table .= '<br><b>Author:</b> '.(isset($author)?$author:'');
        $table .= (isset($url)?' <b>Url:</b> <a href="'.$url.'" target="_blank">'.$url.'</a>':'');
        $table .= (isset($contact)?' <b>Contact:</b> '.$contact:'');
    }

    if (in_array($p,$GLOBALS['config']['packages'])) {
        //if (new_version) $table .= 'Upgrade<br>';
        $table .= '<td>';
        if(isset($pac->options) || isset($options)) $table .= "<a onclick='addon_options(\"{$p}\")' class='g-btn' style='display:inline-flex'><i class='fa fa-gears'></i>&nbsp;Options</a>";
        $table .= "<td><a onclick='addon_deactivate(\"{$p}\")' class='g-btn error'>Deactivate</a>";
    }
    else {
        $table .= "<td><td><a onclick='addon_activate(\"{$p}\")' class='g-btn success'>Activate</a>";
    }
    $pn++;
}
?>
<?=$alert?>

<ul class="g-nav g-tabs gs-12" id="addon-tabs">
  <li class="active"><a href="#downloaded">Downloaded</a></li>
  <li><a href="#newest">Newest</a></li>
</ul>
<div class="tab-content gs-12">
  <div id="downloaded">
    <table class='g-table'><?=$table?></table>
  </div>
  <div id="newest" data-src="admin/packages"></div>
</div>


<script>
function addon_activate(p){ g.ajax('admin/addons?g_response=content&activate='+p,function(x){
    g.alert('Package successfully activated!','success','location.reload(true)');
})};
function addon_deactivate(p){ g.ajax('admin/addons?g_response=content&deactivate='+p,function(x){
    g.alert('Package deactivated!','notice','location.reload(true)');
})};
function addon_download(p){ g.ajax('admin/addons?g_response=content&download='+p,function(x){
    // something to show progress
    if(x=='ok')
      g.alert('Package downloaded!','success');
    else   g.alert('Package not downloaded!','warning');
    this.style.color="#000";
})};

g.dialog.buttons.save_options = {
    title:'Save Options',fn:function(){
		let p = g.el('addon_id').value;
		let fm=new FormData(g.el('addon_options_form'))
        g.ajax({url:'admin/addons?g_response=content&save_options='+p,method:'POST',data:fm,fn:function(x){
			g('.gila-darkscreen').remove();
		}})
    }
}

function addon_options(p) {
 g.post("admin/addons",'g_response=content&options='+p,function(x){
     g.modal({title:"Options",body:x,buttons:'save_options'})
 })
}

g.click('#addon-tabs a',function(){
    g(event.target).findUp('.g-tabs').children().removeClass('active');
    g(event.target).findUp('li').addClass('active');
    event.preventDefault();
    hash=event.target.href.split('#');
    if(typeof hash[1]!=='undefined') if(hash[1]!==''){
        x='#'+hash[1];
        g(x).parent().children().style('display','none');
        g(x).style('display','block');
        if (g(x).attr('data-src')) g.post(g(x).attr('data-src'),'',function(response){
            g(x).all[0].innerHTML=response
        })
    }
    return false;
})

</script>
