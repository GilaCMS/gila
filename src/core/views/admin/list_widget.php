<link rel="stylesheet" type="text/css" href="lib/pnk/pnk.css?">
<div class='pnk-table' pnk-src='src/core/tables/widget' id='twidget'></div>

<script src='lib\jquery\jquery-2.2.4.min.js'></script>
<script src='lib\pnk\pnk-1.3.js?v=4'></script>

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
		g('#gila-darkscreen').remove();
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
