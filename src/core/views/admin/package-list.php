<style>.addon-i{opacity:0.2}.logo-3x{min-width:1em}</style>
<?php
$dir = "src/";
$table = '';
$pn = 0;

function dl_btn($param, $class, $text)
{
  return "<a onclick='addon_download(\"$param\")' class='g-btn $class'>$text</a>";
}

if (Package::check4updates()) {
  $upgrated = 0;
  $upgrateList = json_decode(file_get_contents(LOG_PATH.'/packages2update.json'), true);
  $upgrateN = count($upgrateList);

  foreach ($upgrateList as $newp=>$newv) {
    if (is_string($newp)) {
      if (isset($packages[$newp])) {
        if (version_compare($newv, $packages[$newp]->version) == 1) {
          $logo = $dir."$newp/logo.png";
          $alert = "<img src='lzld/thumb?src=$logo' style='width:40px;float:left'>&nbsp;&nbsp;<b>";
          $alert .= $packages[$newp]->title.'</b> '.__("is_available_on_version");
          $alert .= " $newv &nbsp;&nbsp; ".dl_btn($packages[$newp]->package, 'warning', __('Upgrade'));
          $alert .= '&nbsp;&nbsp;<a href="https://gilacms.com/addons/package/';
          $alert .= $packages[$newp]->package.'" class="g-btn info" target="_blank">'.__('Info').'</a>';
          View::alert('success', $alert);
        } else {
          $upgrated++;
        }
      } else {
        $upgrateN--;
      }
    }
  }
  if ($upgrateN === $upgrated) {
    unlink(LOG_PATH.'/packages2update.json');
  }
}


foreach ($packages as $pkey=>$p) {
  if ($p->package!='core' || Config::get('env')=='dev') {
    if (isset($p->lang)) {
      Config::addLang($p->lang);
    }

    // Border color
    if (file_exists('src/'.$p->package)) {
      if (in_array($p->package, $GLOBALS['config']['packages'])) {
        $border = "border-left:4px solid forestgreen;";
      } else {
        $border = "border-left:4px solid lightgrey";
      }
    } else {
      $border = "";
    }
    $table .= '<tr>';
    $table .= '<td style="color:grey;text-align:center;width: 3em;'.$border.'">';

    // Logo
    if (file_exists($dir."{$p->package}/logo.png")) {
      $table .= '<img class="fa fa-3x logo-3x" src="'."lzld/thumb?src=src/{$p->package}/logo.png".'"/>';
    } elseif (file_exists($dir."{$p->package}/logo.svg")) {
      $table .= '<img class="fa fa-3x logo-3x" src="'."lzld/thumb?src=src/{$p->package}/logo.svg".'"/>';
    } elseif (isset($p->logo) && $p->logo!='') {
      $table .= '<img class="fa fa-3x logo-3x" src="'.($p->logo).'" />';
    } else {
      $table .= '<i class="fa fa-3x fa-dropbox"></i>';
    }

    // Title & version
    $title = $p->title?:$p->package;
    $table .= '<td style="min-width:50%;"><b>'.$title.' '.(isset($p->version)?$p->version:'').'</b>';

    // Description
    $desc = __($p->package.':desc');
    if ($desc==$p->package.':desc') {
      $desc = $p->description?:'No description';
    }
    $table .= '<br>'.$desc.'<br>';

    // Additional info
    $table .= (@$p->author?'<i class="fa fa-user addon-i"></i> '.$p->author:'');
    $table .= (@$p->url?'<i class="fa fa-link addon-i"></i> <a href="'.$p->url.'" target="_blank">'.$p->url.'</a>':'');
    $table .= (isset($p->contact)?' <i class="fa fa-envelope addon-i"</i> '.$p->contact:'');
    if (isset($p->require)) {
      $table .= "<br>Requires: ";
      foreach ($p->require as $req=>$ver) {
        $table .= $req."($ver) ";
      }
    }
    $table .= (isset($p->contact)?' <b>Contact:</b> '.$p->contact:'');
    $table .= '<td style="min-width:50%;">';

    // Buttons
    if (file_exists('src/'.$p->package)) {
      if (in_array($p->package, $GLOBALS['config']['packages']) || $p->package=='core') {
        if (Config::get('env')=='dev') {
          $table .= " <a onclick='addon_activate(\"{$p->package}\")' class='g-btn primary'><i class='fa fa-refresh'></i></a>";
        }
        if ($p->package!='core') {
          $table .= " <a onclick='addon_deactivate(\"{$p->package}\")' class='g-btn warning'>".__('Deactivate')."</a>";
        }
      } else {
        if ($p->package!='core') {
          $table .= " <a onclick='addon_activate(\"{$p->package}\")' class='g-btn primary'>".__('Activate')."</a>";
        }
      }
      if (isset($p->options)) {
        $table .= " <a onclick='addon_options(\"{$p->package}\")' class='g-btn btn-white' style='display:inline-flex'><i class='fa fa-gears'></i></a>";
      }
      if (FS_ACCESS) {
        @$current_version = json_decode(file_get_contents('src/'.$p->package.'/package.json'))->version;
        if ($current_version && version_compare($p->version, $current_version)>0) {
          $table .= ' '.dl_btn($p->package, 'warning', __('Upgrade'));
        }
      }
      $table .= "<td>";
      if (FS_ACCESS) {
        $table .= "<a href='admin/fm/?f=src/{$p->package}' target=\"_blank\" class='g-btn btn-white'><i class=\"fa fa-folder\"></i></a>";
      }
    } else {
      if (FS_ACCESS) {
        $table .= dl_btn($p->package, 'success', __('Download'));
      }
      $table .= '<td>';
    }
    $pn++;
  }
}
?>
<?php
$links=[
['Downloaded','admin/packages'],
['Newest','admin/packages/new']
];
View::alerts();
?>
<div class="row" id='packages-list'>
  <ul class="g-nav g-tabs gs-12" id="addon-tabs"><?php
  foreach ($links as $link) {
    $active = (Router::path()==$link[1]?'active':'');
    echo '<li class="'.$active.'"><a href="'.Config::url($link[1]).'">'.__($link[0]).'</a></li>';
  }
  ?>
    <form method="get" class="inline-flex" style="float:right" action="<?=Config::base('admin/packages/new')?>">
      <input name='search' class="g-input fullwidth" value="<?=(isset($search)?$search:'')?>">
      <button class="g-btn g-group-item" type='submit'><?=__('Search')?></button>
    </form>
  </ul>
  <div class="tab-content gs-12">
    <div><br><table class='g-table' id="tbl-packages" style="margin-left:5px;display:table"><?=$table?></table></div>
  </div>
</div>

<?=View::script('core/admin/media.js')?>
<?=View::script('lib/vue/vue.min.js');?>
<?=View::script('core/admin/vue-components.js');?>

<script>
function addon_activate(p){
  g.loader()
  g.post('admin/packages?g_response=content', 'activate='+p, function(data) {
    g.loader(false)
    response = JSON.parse(data)
    if(response.success==true) {
      g.alert("<?=__('_package_activated')?>",'success','location.reload(true)');
    } else {
      g.alert(response.error, 'warning');
    }
  }
)};

function addon_deactivate(p){
  g.loader()
  g.post('admin/packages?g_response=content','deactivate='+p,function(x){
    g.alert("<?=__('_package_deactivated')?>",'notice','location.reload(true)');
    g.loader(false)
  }
)};

function addon_download(p) {
  g.loader()
  g.post('admin/packages?g_response=content', 'download='+p, function(data){
    g.loader(false)
    response = JSON.parse(data)
    if(response.success==true) {
      g.alert("<?=__('_package_downloaded')?>",'success');
    } else {
      g.alert("<?=__('_package_not_downloaded')?>",'warning');
    }
  }
)};

g.dialog.buttons.save_options = {
  title: '<?=__('Save')?>',fn:function(){
    let p = g.el('addon_id').value;
    let fm=new FormData(g.el('addon_options_form'))
    g.loader()
    g.ajax({url:'admin/packages?g_response=content&save_options='+p,method:'POST',data:fm,fn:function(x){
      g('.gila-darkscreen').remove();
      g.loader(false)
    }})
  }
}

function addon_options(p) {
  g.loader()
  g.post("admin/packages?g_response=content",'options='+p,function(x){
    g.loader(false)
    g.modal({title:'<?=__('Options')?>',body:x,buttons:'save_options'})
    app = new Vue({
      el: '#addon_options_form'
    })
  })
}

var packageListApp = new Vue({
  el: '#packages-list',
  data: {
    selectedPackage: null
  },
  methods: {
    selectPackage: function(x) {
      this.selectedPackage = x
      g.post('admin/packages','html='+x, function(html){
        g.modal({body:html,class:'large'})
      })
    }
  }
});
</script>
