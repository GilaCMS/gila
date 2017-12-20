<link rel="stylesheet" type="text/css" href="lib/pnk/pnk.css?">
<link rel="stylesheet" type="text/css" href="lib/select2/select2.min.css">

<div class='pnk-table' pnk-src='src/core/tables/<?=$table?>' id='tpost'></div>

<script src='lib\jquery\jquery-2.2.4.min.js'></script>
<script src='lib\pnk\pnk-1.3.js?v=4'></script>
<script src='lib\select2\select2.min.js'></script>

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
pnk_populate_tables(document);
</script>
