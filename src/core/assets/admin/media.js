//#main-wrapper
g.click(".gal-image",function(){
    g('.gal-path').removeClass('g-selected');
    g(this).addClass('g-selected');
    g('#selected-path').attr('value',this.getAttribute('data-path'))
})
g.click(".gal-folder",function(){
    let path=this.getAttribute('data-path')
    g.ajax({url:"admin/media",method:"POST",header:"application/x-www-form-urlencoded",data:"g_response=content&path="+path,fn:function(gal){ //
        g('#admin-media-div').parent().html(gal)
    }})
})
g.click("#fm-goup",function(){
    if(this.getAttribute('data-path')=='') return;

    let path=this.getAttribute('data-path')
    g.post("admin/media","g_response=content&path="+path,function(gal){ //
        g('#admin-media-div').parent().html(gal)
    })
})
function gallery_upload_files() {
    let fm=new FormData() //g.el('upload_files_form')
    fm.append('uploadfiles', g.el('upload_files').files[0]);
    fm.append('path', g.el('upload_files').getAttribute('data-path'));
    fm.append('g_response', 'content');
    g.ajax({url:"admin/media_upload",method:'POST',data:fm, fn: function (gal){
        g('#admin-media-div').parent().html(gal)
    }})
}
