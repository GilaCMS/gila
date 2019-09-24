<?php
$dir = "themes/";
$table = '<br><div style="display:grid;grid-gap:15px;grid-template-columns:repeat(auto-fit,minmax(260px,auto))">';
$pn = 0;

foreach ($packages as $pkey=>$p) {
  if ($p->package == gila::config('theme')) $border="border: 2px solid green;"; else $border="";
  $table .= '<div class="bordered wrapper" style="vertical-align: top;'.$border.'">';
  $table .= '<p><strong>'.(isset($p->title)?$p->title:$p->package).' '.(isset($p->version)?$p->version:'').'</strong>';
  if(isset($p->author)) $table .= ' '.__('by').' '.$p->author;
  $table .= '</p><div style="box-shadow:0 0 6px black;">';
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
    if ($p->package!=gila::config('theme')) {
      $table .= "<a onclick='theme_activate(\"{$p->package}\")' class='g-btn default'>".__('Select')."</a> ";
    }
    if(isset($p->options)) {
      $table .= "<a onclick='theme_options(\"{$p->package}\")' class='g-btn' style='display:inline-flex'><i class='fa fa-gears'></i>&nbsp;</a> ";
    }
    if(@$current_version = json_decode(file_get_contents('themes/'.$p->package.'/package.json'))->version) {
      if(version_compare($p->version,$current_version)>0) $table .= " <a onclick='theme_download(\"{$p->package}\")' class='g-btn success'>".__('Upgrade')."</a>";
    }
    $table .= "<a href='".gila::base_url()."?g_preview_theme={$p->package}' target='_blank' class='g-btn btn-white' style='display:inline-flex'><i class='fa fa-eye'></i>&nbsp;</a> ";
    if(FS_ACCESS) $table .= "<a href='admin/fm/?f=themes/{$p->package}' target=\"_blank\" class='g-btn btn-white'><i class=\"fa fa-folder\"></i></a>";
  } else {
    $table .= "<a onclick='theme_download(\"{$p->package}\")' class='g-btn success'>".__('Download')."</a>";
  }
  if(isset($p->parent)) $table .= "<br>Parent: ".$p->parent;
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
    echo '<li class="'.$active.'"><a href="'.gila::url($link[1]).'">'.__($link[0]).'</a></li>';
  }
  ?>
    <form method="get" class="inline-flex" style="float:right" action="<?=gila::url('admin/newthemes')?>">
      <input name='search' class="g-input fullwidth" value="<?=($search??'')?>">
      <button class="g-btn g-group-item" onclick='submit'>Search</button>
    </form>
  </ul>
  <div class="tab-content gs-12">
    <div>
      <?=$table?>
    </div>
  </div>
</div>


<?=view::script('src/core/assets/admin/media.js')?>
<?=view::script('lib/vue/vue.min.js');?>
<?=view::script('src/core/lang/content/'.gila::config('language').'.js');?>
<?=view::script('src/core/assets/admin/listcomponent.js');?>
<script>
function theme_activate(p) {
  g.loader()
  g.ajax('admin/themes?g_response=content&activate='+p,function(x){
    g.loader(false)
    g.alert("<?=__('_theme_selected')?>",'success','location.reload(true)');
})};
function theme_download(p){
  g.loader()
  g.ajax('admin/themes?g_response=content&download='+p, function(x) {
    g.loader(false)
    if(x=='ok')
      g.alert("<?=__('_theme_downloaded')?>",'success');
    else
      g.alert(x,'warning');
  }
)};

g.dialog.buttons.save_options = {
  title:'<?=__('Save')?>', fn:function() {
    let p = g.el('theme_id').value;
    let fm=new FormData(g.el('theme_options_form'))
    g.loader()
    g.ajax({url:'admin/themes?g_response=content&save_options='+p,method:'POST',data:fm,fn:function(x){
      g.loader(false)
      g('.gila-darkscreen').remove();
    }})
  }
}

function theme_options(p) {
  g.loader()
  g.post("admin/themes",'g_response=content&options='+p,function(x){
    g.loader(false)
    g.modal({title:"<?=__('Options')?>",body:x,buttons:'save_options',type:'modal'})
    app = new Vue({
      el: '#theme_options_form'
    })
  })
}

</script>
