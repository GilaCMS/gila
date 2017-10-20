
<link href="lib/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="lib/vue/vue-editor.css">
<script src="lib/vue/vue.min.js"></script>
<!--script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.16/vue.js"></script-->
<script src="lib/vue/vue-editor.js"></script>

<div id="editor"  class="gs-12" style="height:350px">
    <vue-editor text='<?=isset($p->page)?$p->page:$p->post?>'></vue-editor>
</div>

<script>
function get_text_to_save() {
    return g('[name=p_post]').all[0].innerHTML;
}

var ve_editor = new Vue({
	el: '#editor',
})

if(typeof requiredRes=='undefined') requiredRes = {}

g.require('lib/jquery/jquery-2.2.4.min.js',function(){
    g.loadCSS('lib/select2/select2.min.css');
    //g.loadCSS('lib/font-awesome/css/font-awesome.min.css');
    //g.loadCSS('lib/vue/vue-editor.css');
    g.require(['lib/select2/select2.min.js'],function(){
        $('.select2').select2();
    })
    $(document).on('click','button',function(){return false})
})

g.click('button[index]',function(){return false})

g.dialog.buttons.select_path = {
    title:'Select',fn: function(){
        let v = g('#selected-path').attr('value')
        if(v!=null) g('[name=p_img]').attr('value',v)
        g('#media_dialog').remove();
    }
}
g.dialog.buttons.select_path_post = {
    title:'Select', fn: function() {
        let v = g('#selected-path').attr('value')
        if(v!=null) input_filename(v);
        g('#media_dialog').remove();
    }
}
function open_gallery() {
    g.post("admin/media","g_response=content&path=assets",function(gal){
        g.dialog({title:"Gila gallery",body:gal,buttons:'select_path',id:'media_dialog'})
    })
}
function open_gallery_post() {
    g.post("admin/media","g_response=content&path=assets",function(gal){ //
        g.dialog({title:"Gila gallery",body:gal,buttons:'select_path_post',id:'media_dialog'})
    })
}

</script>
