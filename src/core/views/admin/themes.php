<?php
$dir = "themes/";
$packages = scandir($dir);

$table = '';
$pn = 0; $alert = '';

$activate = router::get('activate');
if (in_array($activate,$packages)) {
    if(!in_array($activate, $GLOBALS['config']['packages'])) {
        $GLOBALS['config']['theme']=$activate;
        gila::updateConfigFile();
        usleep(100);
        exit;
    }
}

$download = router::get('download');
if ($download) {
  $zip = new ZipArchive;
  $target = 'themes/'.$download;
  $file = 'http://gilacms.com/assets/themes/'.$download.'.zip';
  $localfile = 'theme/'.$download.'.zip';
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

$options = router::post('options');
if ($options==gila::config('theme')) {
    echo '<form id="theme_options_form" class="g-form"><input id="theme_id" value="'.$options.'" type="hidden">';
    $pack=$options;
    include __DIR__.'/../../../../themes/'.$options.'/package.php';
    foreach($options as $key=>$op) {
        echo '<div class="gm-12">';
        echo '<label class="gm-4">'.(isset($op['title'])?$op['title']:ucwords($key)).'</label>';
        $ov = gila::option('theme.'.$key);
        if(!$ov) if(isset($op['default'])) $ov = $op['default'];

        if(isset($op['type'])) {
            if($op['type']=='select') {
                if(!isset($op['options'])) die("<b>Option $key require options</b>");
                echo '<select class="g-input gm-8" name="option['.$key.']">';
                foreach($op['options'] as $value=>$name) {
                    echo '<option value="'.$value.'"'.($value==$ov?' selected':'').'>'.$name.'</option>';
                }
                echo '</select>';
            }
            if($op['type']=='media') { ?>
                <div class="gm-10 g-group">
                  <span class="btn g-group-item" style="width:28px" onclick="open_media_gallery('#m_<?=$key?>')"><i class="fa fa-image"></i></span>
                  <span class="g-group-item"><input class="fullwidth" value="<?=$ov?>" id="m_<?=$key?>" name="option[<?=$key?>]"><span>
                </span></span></div>
      <?php }
        } else echo '<input class="g-input gm-8" name="option['.$key.']" value="'.$ov.'">';
        echo '</div><br>';
    }
    echo "</form>";
    return;
}


$save_options = router::get('save_options');
if ($save_options==gila::config('theme')) {
	global $db;
	foreach($_POST['option'] as $key=>$value) {
		$ql="INSERT INTO `option`(`option`,`value`) VALUES('theme.$key','$value') ON DUPLICATE KEY UPDATE `value`='$value';";
		$db->query($ql);
	}
    return;
}


foreach ($packages as $p) if($p[0] != '.') if(file_exists($dir."$p/package.php")){
    $table .= '<tr>';
    if (file_exists($dir."$p/screenshot.png")) {
        $table .= '<td style="width:33%"><div ><img src="'."themes/$p/screenshot.png".'"  /></div>';
    }
    else {
        $table .= '<td>';
    }

    if(file_exists($dir."$p/package.json")) {
        $pac=json_decode(file_get_contents($dir."$p/package.json"));
        $table .= '<td style="width:66%"><h4>'.($pac->name?:$p).' '.($pac->version?:'');
        $table .= '</h4>'.(isset($pac->description)?$pac->description:'No description');
        $table .= '<br><b>Author:</b> '.(isset($pac->author)?$pac->author:'');
        $table .= (isset($pac->url)?' <b>Url:</b> <a href="'.$pac->url.'" target="_blank">'.$pac->url.'</a>':'');
        $table .= (isset($pac->contact)?' <b>Contact:</b> '.$pac->contact:'');
        unset($options);
    }else{
        include $dir."$p/package.php";
        $table .= '<td style="width:66%"><h4>'.($name?:$p).' '.($version?:'');
        $table .= '</h4>'.(isset($description)?$description:'No description');
        $table .= '<br><b>Author:</b> '.(isset($author)?$author:'');
        $table .= (isset($url)?' <b>Url:</b> <a href="'.$url.'" target="_blank">'.$url.'</a>':'');
        $table .= (isset($contact)?' <b>Contact:</b> '.$contact:'');
    }

    if ($p==gila::config('theme')) {
        $table .= "<td><a onclick='theme_options(\"{$p}\")' class='g-btn' style='display:inline-flex'><i class='fa fa-gears'></i>&nbsp;Options</a><td>";
        $table .= "<a onclick='#' class='g-btn success'>Selected</a>";
    }
    else {
        $table .= "<td><td><a onclick='theme_activate(\"{$p}\")' class='g-btn default'>Select</a>";
    }
    $pn++;
}
?>
<?=$alert?>

<ul class="g-nav g-tabs gs-12" id="theme-tabs">
  <li class="active"><a href="#downloaded">Downloaded</a></li>
  <li><a href="#newest">Newest</a></li>
</ul>
<div class="tab-content gs-12">
  <div id="downloaded">
    <table class='g-table'><?=$table?></table>
  </div>
  <div id="newest" data-src="admin/newthemes"></div>
</div>

<script>
function theme_activate(p){ g.ajax('admin/themes?g_response=content&activate='+p,function(x){
    g.alert('Theme selected!','success','location.reload(true)');
})};
function theme_download(p){ g.ajax('admin/themes?g_response=content&download='+p,function(x){
    // something to show progress
    if(x=='ok')
      g.alert('Theme downloaded!','success');
    else  g.alert('Theme not downloaded!','warning');
    this.style.color="#000";
})};

g.dialog.buttons.save_options = {
    title:'Save Options',fn:function(){
		let p = g.el('theme_id').value;
		let fm=new FormData(g.el('theme_options_form'))
        g.ajax({url:'admin/themes?g_response=content&save_options='+p,method:'POST',data:fm,fn:function(x){
			  g('.gila-darkscreen').remove();
		}})
    }
}

function theme_options(p) {
 g.post("admin/themes",'g_response=content&options='+p,function(x){
     g.modal({title:"Options",body:x,buttons:'save_options',type:'modal'})
 })
}

g.click('#theme-tabs a',function(){
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
