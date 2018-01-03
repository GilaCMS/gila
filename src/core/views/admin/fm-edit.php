<link href="lib/CodeMirror/codemirror.css" rel="stylesheet">

<h3><?=$c->filepath?></h3>
<div>
    <textarea id="textarea">
        <?php readfile($c->filepath)?>
    </textarea>
</div>


<script src="lib/CodeMirror/codemirror.js"></script>
<script src="lib/CodeMirror/javascript.js"></script>
<script src="lib/CodeMirror/css.js"></script>
<script src="lib/CodeMirror/xml.js"></script>
<script src="lib/CodeMirror/php.js"></script>
<script src="lib/CodeMirror/htmlmixed.js"></script>
<script>
requiredRes = new Array()
var myCodeMirror = new Array();
var saveFilePath;

mode = 'php';
mirror = CodeMirror.fromTextArea(document.getElementById('textarea'),{
    lineNumbers:true, mode:mode
});

</script>
