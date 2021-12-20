<?php
$gtable = new Table($table);
$t = $gtable->getTable();
if (!$gtable->can('read')) {
  @http_response_code(404);
  echo '<h1>403 Error</h1>';
  echo '<h2>You cannot access this content</h2>';
  return;
}
View::css('core/admin/content.css');
View::css('core/admin/vue-editor.css');
View::script('lib/vue/vue.min.js');
View::script('core/admin/content.js');
View::script('core/lang/content/'.Config::lang().'.js');
View::scriptAsync('core/admin/media.js');
View::scriptAsync('core/admin/vue-components.js');
View::script('core/vue-upload-media.js');
View::scriptAsync('core/admin/vue-editor.js');
View::script('lib/CodeMirror/codemirror.js');
View::script('lib/vue/vue-select.js');
View::scriptAsync('lib/CodeMirror/htmlmixed.js');
View::scriptAsync('lib/CodeMirror/javascript.js');
View::cssAsync('lib/CodeMirror/codemirror.css');
View::cssAsync('lib/vue/vue-select.css');
?>
<style>.CodeMirror{max-height:300px;border:1px solid var(--main-border-color);width:100%}</style>
<?=View::scriptAsync("lib/tinymce5/tinymce.min.js")?>

<style>
.type-textarea label{width:100%}
.edit-item-form>.type-tinymce{min-height:300px;margin-bottom:20px}
.edit-item-form>.type-tinymce,.edit-item-form>.type-list{grid-column:1/-1}
.mce-tinymce.mce-container.mce-panel{display:inline-block}
@media only screen and (min-width:700px){
  #user-post-edit-item-form>div,
  #post-edit-item-form>div{
    grid-template-columns: 2fr 2fr 2fr 1fr 1fr 1fr!important;
    min-height:60vh;
  }
  #user-post-edit-item-form>div>div,
  #post-edit-item-form>div>div{
    grid-column:span 3;
  }
  .gila-popup #user-post-edit-item-form .type-tinymce,
  .gila-popup #post-edit-item-form .type-tinymce{grid-column:1/4;grid-row:1/20}
}
.tox .tox-menubar,.tox .tox-toolbar, .tox .tox-toolbar__overflow, .tox .tox-toolbar__primary{
  background-color: #f0f0f0;
}
.g-form .vs__search{
  background: inherit;
  border: inherit;
  padding: inherit;
}
.v-select {display:inline-block;min-width:180px}
#select_row_dialog .body{padding:0}
</style>

<?php
foreach ($t['js'] as $js) {
  echo "<script>".file_get_contents($js)."</script>";
}
foreach ($t['css'] as $css) {
  View::css($css);
}

// read the url query and add it in filters
$tableFilters = is_array($t['filters']) ? array_merge($t['filters'], $_GET) : $_GET;
unset($tableFilters['p']);
unset($tableFilters['page']);
View::widgetArea('content.'.$table);
?>
<script>
g.language = '<?=Config::lang()?>';
</script>
<div id="vue-table">
  <g-table gtype="<?=$table?>" ref="gtable"
  gtable="<?=htmlspecialchars(json_encode($t))?>"
  gfilter="<?=htmlspecialchars(json_encode($tableFilters))?>"
  gfields="<?=htmlspecialchars(json_encode($gtable->fields('list')))?>"
  grows="<?=htmlspecialchars(json_encode($gtable->getRowsIndexed($tableFilters, ['page'=>1])))?>"
  permissions="<?=htmlspecialchars(json_encode(Gila\Session::permissions()))?>"
  gtotalrows="<?=$gtable->totalRows($tableFilters)?>"
  base="<?=Config::base()?>admin/content/<?=$table?>"></g-table>
</div>

<script>
Vue.component('v-select', VueSelect.VueSelect);
cmirror=new Array()
mce_editor=new Array()
var csrfToken = '<?=Form::getToken()?>'
var app = new Vue({
  el:"#vue-table"
})

g_tinymce_options.templates = <?php echo json_encode((isset($templates)?$templates:[])); ?>;

base_url = "<?=Config::get('base')?>"
g_tinymce_options.document_base_url = "<?=Config::get('base')?>"
g_tinymce_options.height = '100%'

window.onpopstate = function(event) {
  console.log("location: " + document.location + ", state: " + JSON.stringify(event.state));
  location.reload()
}

</script>

<?=View::scriptAsync('core/lazyImgLoad.js');?>
