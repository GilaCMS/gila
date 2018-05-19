<?php
$dir = "themes/";
$table = '<br><div style="display:grid;grid-gap:15px;grid-template-columns:repeat(auto-fit,minmax(260px,auto))">';
$pn = 0;

foreach ($packages as $pkey=>$p) {
    if ($p->package == gila::config('theme')) $border="border: 2px solid green;"; else $border="";
    $table .= '<div class="bordered wrapper" style="vertical-align: top;'.$border.'">';
    $table .= '<h4>'.(isset($p->name)?$p->name:$p->package).' '.(isset($p->version)?$p->version:'').'</h4>';
    $table .= '<div style="background:lightgrey;">';
    if (file_exists($dir.$p->package."/screenshot.jpg")) {
        $table .= '<img src="'."themes/{$p->package}/screenshot.jpg".'"  />';
    }
    else if (file_exists($dir.$p->package."/screenshot.png")) {
        $table .= '<img src="'."themes/{$p->package}/screenshot.png".'"  />';
    } else if (isset($p->screenshot)) {
        $table .= '<img src="'.$p->screenshot.'"  />';
    }

    $table.="</div><br>";

    if (file_exists('themes/'.$p->package)) {
        if ($p->package==gila::config('theme')) {
            $table .= "<a onclick='theme_options(\"{$p->package}\")' class='g-btn btn-success' style='display:inline-flex'><i class='fa fa-gears'></i>&nbsp;Options</a> ";
        }
        else {
            $table .= "<a onclick='theme_activate(\"{$p->package}\")' class='g-btn default'>Select</a> ";
            $table .= "<a href='".gila::config('base')."?g_preview_theme={$p->package}' target='_blank' class='g-btn btn-white' style='display:inline-flex'><i class='fa fa-eye'></i>&nbsp;Preview</a> ";
        }
        $current_version = json_decode(file_get_contents('themes/'.$p->package.'/package.json'))->version;
        if(version_compare($p->version,$current_version)>0) $table .= " <a onclick='theme_download(\"{$p}\")' class='g-btn success'>Upgrade</a>";
        $table .= "<a href='fm/?path=themes/{$p->package}' target=\"_blank\" class='g-btn g-white'><i class=\"fa fa-folder\"></i></a>";
    } else {
        $table .= "<a onclick='theme_download(\"{$p->package}\")' class='g-btn success'>Download</a>";
    }
    $pn++;
    $table .= "</div>";
}

$links=[
['Downloaded','admin/themes'],
['Newest','admin/newthemes']
];
view::alerts();
?>
<div class="row">
    <ul class="g-nav g-tabs gs-12" id="theme-tabs"><?php
    foreach($links as $link){
        $active = (router::url()==$link[1]?'active':'');
        echo '<li class="'.$active.'"><a href="'.gila::url($link[1]).'">'.$link[0].'</a></li>';
    }
    ?>
    </ul>
    <div class="tab-content gs-12">
        <div class=''><?=$table?></div>
    </div>
</div>


<?=view::script('src/core/assets/admin/media.js')?>
<script>
function theme_activate(p){ g.ajax('admin/themes?g_response=content&activate='+p,function(x){
    g.alert('Theme selected!','success','location.reload(true)');
})};
function theme_download(p){ g.ajax('admin/themes?g_response=content&download='+p,function(x){
    // something to show progress
    if(x=='ok')
      g.alert('Theme downloaded!','success');
    else  g.alert('Theme not downloaded!','warning');
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


</script>
