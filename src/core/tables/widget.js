cmirror=new Array()

g.dialog.buttons.update_widget = {title:'Update',fn:function(){
	textareas=g('.codemirror-js').all
	for(i=0;i<textareas.length;i++) {
		textareas[i].value=cmirror[i].getValue()
	}
    let fm=new FormData(g.el('widget_options_form'))
    let _app=app

    g.ajax({url:'admin/update_widget?g_response=content',method:'POST',data:fm,fn:function(data){
		g('#gila-popup').parent().remove();
        data = JSON.parse(data)
        widget_dialog_edit_table.update_row(data.rows[0])
        widget_dialog_edit_table.$forceUpdate()
	}})
}}

gtableCommand.edit_widget = {
    fa: "pencil",
    title: "Edit",
    fn: function(table,id){
        href='admin/widgets?id='+id;
        widget_dialog_edit_table = table;
        g.ajax(href,function(data){
            g.dialog({class:'lightscreen large',body:data,type:'modal',buttons:'update_widget'})
            app = new Vue({
                el: '#widget_options_form'
            })
            textareas=g('.codemirror-js').all
            for(i=0;i<textareas.length;i++) {
                cmirror[i]=CodeMirror.fromTextArea(textareas[i],{lineNumbers:true,mode:'javascript'});
            }
        });
    }
};