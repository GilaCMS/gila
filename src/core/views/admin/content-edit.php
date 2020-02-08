<?=view::css('src/core/assets/admin/content.css')?>
<?=view::cssAsync('lib/select2/select2.min.css')?>
<?=view::cssAsync('src/core/assets/admin/vue-editor.css')?>

<?=view::script('lib/jquery/jquery-3.3.1.min.js')?>
<?=view::script('lib/select2/select2.min.js','async')?>
<?=view::script('lib/vue/vue.min.js')?>

<script src="lib/CodeMirror/codemirror.js"></script>
<link rel="stylesheet" href="lib/CodeMirror/codemirror.css">
<style>.CodeMirror{max-height:150px;border:1px solid var(--main-border-color);width:100%}</style>
<script src="lib/CodeMirror/javascript.js"></script>
<?=view::script("lib/tinymce/tinymce.min.js")?>

<?php
view::script('src/core/assets/admin/media.js');
view::script('src/core/assets/admin/content.js');
if(file_exists('src/'.$tablesrc.'.js')) view::script('src/'.$tablesrc.'.js');
view::script('src/core/lang/content/'.gila::config('language').'.js');
view::script('src/core/assets/admin/listcomponent.js');
view::script('src/core/assets/admin/vue-editor.js');
?>

<style>
.type-textarea label,.type-paragraph label{width:100%}
.type-tinymce,.type-textarea,.type-paragraph{grid-column:1/-1}
.mce-tinymce.mce-container.mce-panel{display:inline-block}
</style>

<?php
global $db;
$pnk = new gTable($table, core\models\user::permissions(session::user_id()));
$t = $pnk->getTable();
$pages_path = [];
$templates = [];

foreach($t['js'] as $js) view::script($js);
foreach($t['css'] as $css) view::css($css);

$pages_path[] = view::getThemePath().'/pages/';
if(view::$parent_theme) $pages_path[] = 'themes/'.view::$parent_theme.'/templates/';
$pages_path = array_merge($pages_path, gila::packages());
$pages_path[] = 'src/core/templates/';
foreach($pages_path as $path) {
  if(file_exists($path)) {
    $pages = scandir($path);
    foreach ($pages as $page) if($page[0]!='.'){
      $templates[] = [
        'title'=>$page, 'url'=>$path.$page
      ];
    }
  }
}

$fields = $pnk->fields('edit');
echo '<form id="'.$table.'-edit-item-form" data-table="'.$table.'" data-id="'.$id.'" class="g-form"><div>';
if($id) {
  $ql = "SELECT {$pnk->select($fields)} FROM {$pnk->name()} WHERE id=$id;";
  $res = $db->get($ql)[0];
  echo gForm::html($pnk->getFields('edit'),$res);
} else {
  echo gForm::html($pnk->getFields('edit'));
}
echo '</div></form>';

?>


<script>

cmirror=new Array()
mce_editor=new Array()

g_tinymce_options = {
  selector: '',
  relative_urls: false,
  remove_script_host: false,
  height: 250,
  theme: 'modern',
  extended_valid_elements: 'script,div[v-for|v-if|v-model|style|class|id|data-load]',
  plugins: [
    'lists link image hr anchor pagebreak',
    'searchreplace wordcount visualchars code',
    'insertdatetime media nonbreaking table contextmenu ',
    'template paste textcolor textpattern codesample'
  ],
  toolbar1: 'styleselect | forecolor backcolor bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link media image codesample',
  file_picker_callback: function(cb, value, meta) {
    input_filename = cb;
    open_gallery_post();
  },
}
g_tinymce_options.templates = <?php echo json_encode((isset($templates)?$templates:[])); ?>;

base_url = "<?=gila::config('base')?>"
g_tinymce_options.document_base_url = "<?=gila::config('base')?>"

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

<?=view::script('src/core/assets/lazyImgLoad.js');?>
<script>
var app = new Vue({
  el: 'vue-editor'
});
</script>
