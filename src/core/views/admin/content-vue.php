<?=view::css('src/core/assets/admin/content.css')?>
<?=view::cssAsync('lib/select2/select2.min.css')?>

<?=view::script('lib\jquery\jquery-3.3.1.min.js')?>
<?=view::script('lib\select2\select2.min.js','async')?>
<?=view::script('lib\vue\vue.min.js')?>

<script src="lib/CodeMirror/codemirror.js"></script>
<link rel="stylesheet" href="lib/CodeMirror/codemirror.css">
<style>.CodeMirror{max-height:150px;border:1px solid var(--main-border-color);width:100%}</style>
<script src="lib/CodeMirror/javascript.js"></script>
<?=view::script("lib/tinymce/tinymce.min.js")?>

<?=view::script('src/core/assets/admin/media.js')?>
<?=view::script('src/core/assets/admin/content.js');?>
<?=view::script('src/'.$tablesrc.'.js');?>
<?=view::script('src/core/lang/content/'.gila::config('language').'.js');?>
<?=view::script('src/core/assets/admin/listcomponent.js');?>

<style>
.type-tinymce,.type-textarea{grid-column:1/-1}
.mce-tinymce.mce-container.mce-panel{display:inline-block}
</style>

<?php
$pnk = new gTable($table);
$t = $pnk->getTable();
$pages_path = [];
$templates = [];

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
?>

<div id="vue-table">
  <g-table gtype="<?=$table?>" ref="gtable" gtable="<?=htmlspecialchars(json_encode($t))?>" gfields="<?=htmlspecialchars(json_encode($pnk->fields('list')))?>"></g-table>
</div>

<script>

cmirror=new Array()
mce_editor=new Array()

var app = new Vue({
  el:"#vue-table"
})

g_tinymce_options.templates = <?php echo json_encode((isset($templates)?$templates:[])); ?>;

base_url = "<?=gila::config('base')?>"
g_tinymce_options.document_base_url = "<?=gila::config('base')?>"

</script>
