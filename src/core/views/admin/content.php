<?=view::cssAsync('lib/pnk/pnk.css')?>
<?=view::cssAsync('lib/select2/select2.min.css')?>

<div class='pnk-table' pnk-src='src/<?=$tablesrc?>' pnk-table='<?=$table?>' id='tcontent'></div>

<?=view::script('lib\jquery\jquery-3.3.1.min.js')?>
<?=view::script('lib\pnk\pnk-1.4.js')?>
<?=view::script('lib\select2\select2.min.js')?>

<script src="lib/CodeMirror/codemirror.js"></script>
<script src="lib/CodeMirror/javascript.js"></script>
<script src="lib/CodeMirror/css.js"></script>
<script src="lib/CodeMirror/xml.js"></script>
<script src="lib/CodeMirror/php.js"></script>
<script src="lib/CodeMirror/htmlmixed.js"></script>

<script>
PNK.tools.new_post = {title:'New',fn:function(){
    window.location.href='admin/posts/new'
}}
PNK.tools.new_page = {title:'New',fn:function(){
    window.location.href='admin/pages/new'
}}
pnk_populate_tables(document);
</script>
