cmirror=new Array()
mce_editor=new Array()

g.dialog.buttons.update_widget = {title:'Update',fn:function(){
  let fm=new FormData(g.el('widget_options_form'))
  values = readFromClassComponents()
  for(x in values) {
    fm.set(x, values[x])
  }
  let _app=app

  g.ajax({url:'admin/update_widget?g_response=content',method:'POST',data:fm,fn:function(data){
	  g.closeModal();
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
        g.get(href, function(data){
            g.dialog({class:'lightscreen large',body:data,type:'modal',buttons:'update_widget'})
            app = new Vue({
                el: '#widget_options_form'
            })

            g_tinymce_options.height = 160;
            g_tinymce_options.menubar = false
            g_tinymce_options.toolbar = 'format bold italic superscript subscript| alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table link image charmap code'
            transformClassComponents();
        });
    }
};
