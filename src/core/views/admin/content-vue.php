<?=View::css('src/core/assets/admin/content.css')?>
<?=View::cssAsync('lib/select2/select2.min.css')?>

<?=View::script('lib/jquery/jquery-3.3.1.min.js')?>
<?=View::script('lib/select2/select2.min.js','async')?>
<?=View::script('lib/vue/vue.min.js')?>

<script src="lib/CodeMirror/codemirror.js"></script>
<link rel="stylesheet" href="lib/CodeMirror/codemirror.css">
<style>.CodeMirror{max-height:150px;border:1px solid var(--main-border-color);width:100%}</style>
<script src="lib/CodeMirror/javascript.js"></script>
<?=View::script("lib/tinymce/tinymce.min.js")?>

<?php
View::script('src/core/assets/admin/media.js');
View::script('src/core/assets/admin/content.js');
if(file_exists('src/'.$tablesrc.'.js')) View::script('src/'.$tablesrc.'.js');
View::script('src/core/lang/content/'.Gila::config('language').'.js');
View::script('src/core/assets/admin/listcomponent.js');
View::script('src/core/assets/admin/vue-editor.js');
?>

<style>
.type-textarea label{width:100%}
.type-tinymce,.type-textarea{grid-column:1/-1}
.mce-tinymce.mce-container.mce-panel{display:inline-block}
</style>

<?php
$pnk = new gTable($table);
$t = $pnk->getTable();
$pages_path = [];
$templates = [];

foreach($t['js'] as $js) View::script($js);
foreach($t['css'] as $css) View::css($css);

$pages_path[] = View::getThemePath().'/pages/';
if(View::$parent_theme) $pages_path[] = 'themes/'.View::$parent_theme.'/templates/';
$pages_path = array_merge($pages_path, Gila::packages());
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
?>

<div id="vue-table">
  <g-table gtype="<?=$table?>" ref="gtable"
  gtable="<?=htmlspecialchars(json_encode($t))?>"
  gfields="<?=htmlspecialchars(json_encode($pnk->fields('list')))?>"
  grows="<?=htmlspecialchars(json_encode($pnk->getRowsIndexed($t['filters'], ['page'=>1])))?>"
  gtotalrows="<?=$pnk->totalRows($t['filters'])?>"></g-table>
</div>

<script>

cmirror=new Array()
mce_editor=new Array()
var csrfToken = '<?=gForm::getToken()?>'
var app = new Vue({
  el:"#vue-table"
})

g_tinymce_options.templates = <?php echo json_encode((isset($templates)?$templates:[])); ?>;

base_url = "<?=Gila::config('base')?>"
g_tinymce_options.document_base_url = "<?=Gila::config('base')?>"

</script>

<?=View::script('src/core/assets/lazyImgLoad.js');?>
