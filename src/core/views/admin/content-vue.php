<?=View::css('core/admin/content.css')?>
<?=View::cssAsync('core/admin/vue-editor.css')?>
<?=View::script('lib/vue/vue.min.js')?>

<?php
View::script('core/admin/content.js');
if(file_exists('src/'.$tablesrc.'.js')) {
  echo "<script>".file_get_contents('src/'.$tablesrc.'.js')."</script>";
}
View::scriptAsync('core/lang/content/'.Gila::config('language').'.js');
View::scriptAsync('core/admin/media.js');
View::scriptAsync('core/admin/vue-components.js');
View::scriptAsync('core/admin/vue-editor.js');
View::script('lib/CodeMirror/codemirror.js');
View::scriptAsync('lib/CodeMirror/javascript.js');
View::cssAsync('lib/CodeMirror/codemirror.css');
// DEPRECATED the below
View::script('lib/jquery/jquery-3.3.1.min.js');
View::script('lib/select2/select2.min.js');
View::css('lib/select2/select2.min.css');
?>
<style>.CodeMirror{max-height:150px;border:1px solid var(--main-border-color);width:100%}</style>
<?=View::scriptAsync("lib/tinymce/tinymce.min.js")?>

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

<?=View::script('core/lazyImgLoad.js');?>
