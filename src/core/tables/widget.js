cmirror=new Array()
mce_editor=new Array()

g.dialog.buttons.update_widget = {title:'Update',fn:function(){
	textareas=g('.codemirror-js').all
	for(i=0;i<textareas.length;i++) {
		textareas[i].value=cmirror[i].getValue()
	}
  let fm=new FormData(g.el('widget_options_form'))
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
            textareas=g('.codemirror-js').all

            g_tinymce_options = {
              selector: '',
              relative_urls: false,
              remove_script_host: false,
              height: 150,
              theme: 'modern',
              extended_valid_elements: 'script,div[v-for|v-if|v-model|style|class|id|data-load]',
              plugins: [
                'lists link image charmap hr anchor pagebreak',
                'searchreplace wordcount visualchars code',
                'insertdatetime media nonbreaking table contextmenu ',
                'template paste textcolor colorpicker textpattern codesample'
              ],
              toolbar1: 'styleselect | forecolor backcolor bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link media image codesample',
              file_picker_callback: function(cb, value, meta) {
                input_filename = cb;
                open_gallery_post();
              },
            }
            tinymce.remove()
            g_tinymce_options.selector = '[class=tinymce]'
            tinymce.init(g_tinymce_options)
            mce_editor = []
            if(typeof g('.tinymce').all[0] != 'undefined') {
              mce_editor[0] = g('.tinymce').all[0].name
            }
            for(i=0;i<textareas.length;i++) {
                cmirror[i]=CodeMirror.fromTextArea(textareas[i],{lineNumbers:true,mode:'javascript'});
            }
            if(typeof $ != 'undefined'  && typeof $.fn.select2 != 'undefined') $(".select2").select2();
        });
    }
};