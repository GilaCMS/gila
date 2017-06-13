<link rel="stylesheet" href="lib/quill/quill.snow.css" />
<script type="text/javascript" src="lib/quill/quill.min.js"></script>



<div class="gs-12">
    <div id='quill-tab' class="gs-12 quill-tab">
        <?php include __DIR__.'/edit_quill_bar.php'; ?>
        <div id="quill-container" style="width:100%;background:white;height:350px"><?=nl2br(isset($p->page)?$p->page:$p->post)?></div>
    </div>
</div>
<script>
//import jsbeautifier
var code_editor_use = false;

var quill = new Quill('#quill-container', {
    modules: {
        toolbar: '#quill-toolbar-container'
    },
    placeholder: 'Compose an epic...',
    theme: 'snow'
});

function get_text_to_save() {

    return g('#quill-container .ql-editor').all[0].innerHTML;
}


if(typeof requiredRes=='undefined') requiredRes = {}

g.require('lib/jquery/jquery-2.2.4.min.js',function(){
    g.loadCSS('lib/select2/select2.min.css');
    g.require(['lib/select2/select2.min.js'],function(){
        $('.select2').select2();
    })
})

g.dialog.buttons.select_path = {
    title:'Select',fn:function(){
        let v = g('#selected-path').attr('value')
        if(v!=null) g('[name=p_img]').attr('value',v)
        g('#gila-darkscreen').remove();
    }
}
g.dialog.buttons.select_path_post = {
    title:'Select',fn:function(){
        if(!quill.getSelection()) return
        index=quill.getSelection().index
        let v = g('#selected-path').attr('value')
        if(v!=null) quill.insertEmbed(index, 'image', v);
        g('#gila-darkscreen').remove();
    }
}
g.click(".gal-image",function(){
    g('.gal-path').removeClass('g-selected');
    g(this).addClass('g-selected');
    g('#selected-path').attr('value',this.getAttribute('data-path'))
})
g.click(".gal-folder",function(){
    let path=this.getAttribute('data-path')
    g.ajax({url:"admin/media",method:"POST",header:"application/x-www-form-urlencoded",data:"path="+path,fn:function(gal){ //
        g('#gila-popup>.body').html(gal)
    }})
})
g.click("#fm-goup",function(){
    if(this.getAttribute('data-path')=='') return;

    let path=this.getAttribute('data-path')
    g.post("admin/media","path="+path,function(gal){ //
        g('#gila-popup>.body').html(gal)
    })
})

function gallery_upload_files() {
    let fm=new FormData() //g.el('upload_files_form')
    fm.append('uploadfiles', g.el('upload_files').files[0]);
    fm.append('path', g.el('upload_files').getAttribute('data-path'));
    g.ajax({url:"admin/media_upload",method:'POST',data:fm, fn: function (gal){
        g('#gila-popup>.body').html(gal)
    }})
    /*g.upload("admin/media_upload",fm,function(gal){
        g('#gila-popup>.body').html(gal)
    })*/
}

function open_gallery() {
    g.post("admin/media","path=assets",function(gal){ //
        g.dialog({title:"Gila gallery",body:gal,buttons:'select_path'})
    })
}
function open_gallery_post() {
    g.post("admin/media","path=assets",function(gal){ //
        g.dialog({title:"Gila gallery",body:gal,buttons:'select_path_post'})
    })
}
</script>
