<link rel="stylesheet" type="text/css" href="lib/pnk/pnk.css?">
<div class='pnk-table' pnk-src='src/core/tables/widget' id='twidget'></div>

<?=view::script('lib\jquery\jquery-3.3.1.min.js')?>
<?=view::script('lib\pnk\pnk-1.3.js')?>

<script src="lib/CodeMirror/codemirror.js"></script>
<link rel="stylesheet" href="lib/CodeMirror/codemirror.css">
<script src="lib/CodeMirror/javascript.js"></script>

<script>

pnk_populate_tables(document);
requiredRes = [];
cmirror=new Array()

g.dialog.buttons.update_widget = {title:'Update',fn:function(){
	textareas=g('.codemirror-js').all
	for(i=0;i<textareas.length;i++) {
		textareas[i].value=cmirror[i].getValue()
	}
	let fm=new FormData(g.el('widget_options_form'))
    g.ajax({url:'admin/update_widget?g_response=content',method:'POST',data:fm,fn:function(x){
		g('#gila-popup').remove();
	}})
}}

PNK.commands.edit_widget = { fa: "pencil", title: "Edit", fn: function(e){
    href='admin/widgets?id='+e.row_id;
    g.ajax(href,function(data){
        g.dialog({class:'lightscreen',body:data,buttons:'update_widget'})
		textareas=g('.codemirror-js').all
		for(i=0;i<textareas.length;i++) {
			cmirror[i]=CodeMirror.fromTextArea(textareas[i],{lineNumbers:true,mode:'javascript'});
		}
    });
}};

</script>
