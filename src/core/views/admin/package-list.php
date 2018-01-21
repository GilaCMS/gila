<style>.addon-i{opacity:0.2}</style>
<?php
$dir = "src/";
$table = '';
$pn = 0; $alert = '';



foreach ($packages as $pkey=>$p) if($p->package!='core') {
        $table .= '<tr>';
        $table .= '<td style="color:grey;text-align:center">';
        if (file_exists($dir."{$p->package}/logo.png")) {
            $table .= '<img class="fa fa-3x" src="'."src/{$p->package}/logo.png".'" />';
        }
        else {
            $table .= '<i class="fa fa-3x fa-dropbox"></i>';
        }
        $table .= '<td style="width:100%"><b>'.(isset($p->title)?$p->title:$p->package).' '.(isset($p->version)?$p->version:'');
        $table .= '</b><p>'.(isset($p->description)?$p->description:'No description');
        $table .= '</p><i class="fa fa-user addon-i"></i> '.(isset($p->author)?$p->author:'');
        $table .= (isset($p->url)?' <i class="fa fa-link addon-i"></i> <a href="'.$p->url.'" target="_blank">'.$p->url.'</a>':'');
        $table .= (isset($p->contact)?' <i class="fa fa-envelope addon-i"</i> '.$p->contact:'');
        if(isset($p->require)) {
            $table .= "<br>Requires: ";
            foreach($p->require as $req=>$ver) {
                $table .= $req."($ver) ";
            }
        }
        $table .= (isset($p->contact)?' <b>Contact:</b> '.$p->contact:'');
        unset($options);


        if (file_exists('src/'.$p->package)) {
            if (in_array($p->package,$GLOBALS['config']['packages'])) {
                $table .= "<td><a onclick='addon_options(\"{$p->package}\")' class='g-btn' style='display:inline-flex'><i class='fa fa-gears'></i>&nbsp;Options</a><td>";
                $table .= "<a onclick='addon_deactivate(\"{$p->package}\")' class='g-btn error'>Deactivate</a>";
            } else {
                if($p->package=='core') {
                    $table .= "<td><td>";
                } else $table .= "<td><td><a onclick='addon_activate(\"{$p->package}\")' class='g-btn success'>Activate</a>";
            }
            $current_version = json_decode(file_get_contents('src/'.$p->package.'/package.json'))->version;
            if(version_compare($p->version,$current_version)>0) $table .= " <a onclick='addon_download(\"{$p->package}\")' class='g-btn success'>Upgrade</a>";
            $table .= "<td><a href='fm/?path=src/{$p->package}' target=\"_blank\" class='g-btn g-white'><i class=\"fa fa-folder\"></a>";
        } else {
            $table .= "<td><td><a onclick='addon_download(\"{$p->package}\")' class='g-btn success'>Download</a>";
        }
    $pn++;
}
?>
<?php
$links=[
['Downloads','admin/addons'],
['Newest','admin/addons/new']
];
view::alerts();
?>
<div class="row">
    <ul class="g-nav gs-2 vertical" id="addon-tabs"><?php
    foreach($links as $link){
        $active = (router::url()==$link[1]?'g-selected':'');
        echo '<li class="'.$active.'"><a href="'.gila::url($link[1]).'">'.$link[0].'</a></li>';
    }
    ?></ul>
    <div class="tab-content gs-10">
        <table class='g-table'><?=$table?></table>
    </div>
</div>


<script>
function addon_activate(p){ g.ajax('admin/addons?g_response=content&activate='+p,function(x){
    if(x=='ok')
        g.alert('Package successfully activated!','success','location.reload(true)');
    else
        g.alert(x,'warning');
})};
function addon_deactivate(p){ g.ajax('admin/addons?g_response=content&deactivate='+p,function(x){
    g.alert('Package deactivated!','notice','location.reload(true)');
})};
function addon_download(p){ g.ajax('admin/addons?g_response=content&download='+p,function(x){
    // something to show progress
    if(x=='ok')
        g.alert('Package downloaded!','success');
    else
        g.alert('Package not downloaded!','warning');
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
/*
g.click('#addon-tabs a',function(){
    g(event.target).findUp('.g-nav').children().removeClass('active');
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
*/
</script>
