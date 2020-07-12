<?=Gila\View::css('core/admin/content.css')?>
<?=Gila\View::cssAsync('core/admin/vue-editor.css')?>
<?=Gila\View::script('lib/vue/vue.min.js')?>

<?=Gila\View::script('lib/CodeMirror/codemirror.js')?>
<?=Gila\View::script('lib/CodeMirror/javascript.js')?>
<?=Gila\View::cssAsync('lib/CodeMirror/codemirror.css')?>
<style>.CodeMirror{max-height:150px;border:1px solid var(--main-border-color);width:100%}</style>
<?=Gila\View::script("lib/tinymce/tinymce.min.js")?>

<?php
Gila\View::script('core/admin/media.js');
Gila\View::script('core/admin/content.js');
if (file_exists('src/'.$tablesrc.'.js')) {
  echo "<script>".file_get_contents('src/'.$tablesrc.'.js')."</script>";
}
Gila\View::script('core/lang/content/'.Gila\Gila::config('language').'.js');
Gila\View::script('core/admin/vue-components.js');
Gila\View::script('core/admin/vue-editor.js');
?>

<style>
.type-textarea label,.type-paragraph label{width:100%}
.type-tinymce,.type-textarea,.type-paragraph{grid-column:1/-1}
.mce-tinymce.mce-container.mce-panel{display:inline-block}
</style>

<?php
global $db;
$pnk = new Gila\Table($table, Gila\User::permissions(Gila\Session::userId()));
$t = $pnk->getTable();
$pages_path = [];
$templates = [];

foreach ($t['js'] as $js) {
  Gila\View::script($js);
}
foreach ($t['css'] as $css) {
  Gila\View::css($css);
}

$pages_path[] = Gila\View::getThemePath().'/pages/';
if (Gila\View::$parent_theme) {
  $pages_path[] = 'themes/'.Gila\View::$parent_theme.'/templates/';
}
$pages_path = array_merge($pages_path, Gila\Gila::packages());
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
  echo Gila\Form::html($pnk->getFields('edit'), $res);
} else {
  echo Gila\Form::html($pnk->getFields('edit'));
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

base_url = "<?=Gila\Gila::config('base')?>"
g_tinymce_options.document_base_url = "<?=Gila\Gila::config('base')?>"

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

<?=Gila\View::script('core/lazyImgLoad.js');?>
<script>
var app = new Vue({
  el: '#<?=$table?>-edit-item-form'
});
</script>
