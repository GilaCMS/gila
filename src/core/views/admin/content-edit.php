<?=View::css('core/admin/content.css')?>
<?=View::script('lib/vue/vue.min.js')?>

<?=View::script('lib/CodeMirror/codemirror.js')?>
<?=View::script('lib/CodeMirror/javascript.js')?>
<?=View::cssAsync('lib/CodeMirror/codemirror.css')?>
<style>.CodeMirror{max-height:150px;border:1px solid var(--main-border-color);width:100%}</style>
<?=View::script("lib/tinymce5/tinymce.min.js")?>

<?php
View::script('core/admin/media.js');
View::script('core/admin/content.js');
if (file_exists('src/'.$tablesrc.'.js')) {
  echo "<script>".file_get_contents('src/'.$tablesrc.'.js')."</script>";
}
View::script('core/lang/content/'.Config::get('language').'.js');
View::script('core/admin/vue-components.js');
?>

<style>
.type-textarea label,.type-paragraph label{width:100%}
.type-tinymce,.type-paragraph,.type-list{
  grid-column:1/-1
}
.mce-tinymce.mce-container.mce-panel{display:inline-block}
</style>

<?php
global $db;
$pnk = new Table($table, Gila\Session::permissions());
$t = $pnk->getTable();
$pages_path = [];
$templates = [];

foreach ($t['js'] as $js) {
  View::script($js);
}
foreach ($t['css'] as $css) {
  View::css($css);
}

$pages_path[] = View::getThemePath().'/pages/';
if (View::$parent_theme) {
  $pages_path[] = 'themes/'.View::$parent_theme.'/templates/';
}
$pages_path = array_merge($pages_path, Config::packages());
$pages_path[] = 'src/core/templates/';
foreach ($pages_path as $path) {
  if (file_exists($path)) {
    $pages = scandir($path);
    foreach ($pages as $page) {
      if ($page[0]!='.') {
        $templates[] = [
        'title'=>$page, 'url'=>$path.$page
      ];
      }
    }
  }
}

$fields = $pnk->fields('edit');
echo '<form id="'.$table.'-edit-item-form" data-table="'.$table.'" data-id="'.$id.'" class="g-form"><div>';
if ($id) {
  $ql = "SELECT {$pnk->select($fields)} FROM {$pnk->name()} WHERE id=$id;";
  $res = $db->get($ql)[0];
  echo Form::html($pnk->getFields('edit'), $res);
} else {
  echo Form::html($pnk->getFields('edit'));
}
echo '</div></form>';

?>


<script>

cmirror=new Array()
mce_editor=new Array()

g_tinymce_options.templates = <?php echo json_encode((isset($templates)?$templates:[])); ?>;

base_url = "<?=Config::get('base')?>"
g_tinymce_options.document_base_url = "<?=Config::get('base')?>"

transformClassComponents()

function updateRegistry(){
  let irow = <?=$id?>;
  let id_name = '<?=$table?>-edit-item-form';
  
  form = document.getElementById(id_name)
  data = new FormData(form);
  values = readFromClassComponents()
  for(x in values) {
    data.set(x, values[x])
  }
  
  let _this = this
  if(irow=='new') {
    url = 'cm/update_rows/<?=$table?>'
  } else {
    url = 'cm/update_rows/<?=$table?>?id='+irow
  }
  g.ajax({method:'post',url:url,data:data,fn:function(data) {
    data = JSON.parse(data)
    if(irow=='new') {
      alert("Registry created")
    } else {
      alert("Registry updated")
    }
    this.$forceUpdate()
  }})
}
</script>

<button class="btn btn-primary" onclick="updateRegistry()">
Save
</button>

<?=View::script('core/gila.js');?>
<script>
var app = new Vue({
  el: '#<?=$table?>-edit-item-form'
});
</script>
