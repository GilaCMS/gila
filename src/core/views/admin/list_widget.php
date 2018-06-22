<link rel="stylesheet" type="text/css" href="lib/pnk/pnk.css?">
<div class='pnk-table' pnk-src='src/core/tables/widget' pnk-table="widget" id='twidget'></div>

<?=view::script('lib\jquery\jquery-3.3.1.min.js')?>
<?=view::script('lib\pnk\pnk-1.4.js')?>

<script src="lib/CodeMirror/codemirror.js"></script>
<link rel="stylesheet" href="lib/CodeMirror/codemirror.css">
<style>.CodeMirror{max-height:150px;border:1px solid var(--main-border-color);width:100%}</style>
<script src="lib/CodeMirror/javascript.js"></script>
<?=view::script('src/core/assets/admin/media.js')?>
<?=view::script('lib/vue/vue.min.js');?>
<?=view::script('src/core/assets/admin/listcomponent.js');?>
<style>.circle{border-radius: 50%}</style>

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
		g('#gila-popup').parent().remove();
	}})
}}

PNK.commands.edit_widget = { fa: "pencil", title: "Edit", fn: function(e){
    href='admin/widgets?id='+e.row_id;
    g.ajax(href,function(data){
        g.dialog({class:'lightscreen large',body:data,type:'modal',buttons:'update_widget'})
		app = new Vue({
		    el: '#widget_options_form'
		})
		textareas=g('.codemirror-js').all
		for(i=0;i<textareas.length;i++) {
			cmirror[i]=CodeMirror.fromTextArea(textareas[i],{lineNumbers:true,mode:'javascript'});
		}
		if(typeof pnk_populate_tables == 'function') pnk_populate_tables(document);
    });
}};

</script>
