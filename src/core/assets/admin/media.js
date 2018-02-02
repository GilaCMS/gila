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

var media_path_input;
g.dialog.buttons.select_media_path = {
    title:'Select',fn:function(){
        let v = g('#selected-path').attr('value')
        if(v!=null) g(media_path_input).attr('value',v)
        g('#media_dialog').remove();
    }
}
function open_media_gallery(mpi) {
    media_path_input = mpi;
    g.post("admin/media","g_response=content&path=assets",function(gal){
        g.dialog({title:"Gila gallery",body:gal,buttons:'select_media_path',id:'media_dialog'})
    })
}

function filter_files(query,value) {
 var list = document.querySelectorAll(query)
console.log(value+':')
 list.forEach(function(entry){
     console.log(entry.getAttribute('data-path'))
  if(!entry.getAttribute('data-path').includes(value))
   entry.style.display='none';
  else
   entry.style.display='inline-block';
 })
}
