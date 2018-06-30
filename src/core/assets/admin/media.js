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
var media_text = {
    _gallery:'Media Gallery',
    _new_filepath: 'Please enter new file path',
    _file_saved: "File saved successfully",
    _select_file: 'Please select a media file',
    _new_folder: "Create new folder",
    _file_deleted: "File deleted successfully"
}

function __m(m) {
    if(typeof media_text[m] != 'undefined') return media_text[m]; else return m;
}
function gallery_upload_files() {
    let fm=new FormData() //g.el('upload_files_form')
    fm.append('uploadfiles', g.el('upload_files').files[0]);
    fm.append('path', g.el('upload_files').getAttribute('data-path'));
    fm.append('g_response', 'content');
    g.ajax({url:"admin/media_upload",method:'POST',data:fm, fn: function (gal){
        g('#admin-media-div').parent().html(gal)
    }})
}
function gallery_move_selected() {
    selected = g('.g-selected').all[0]
    if(selected) {
        path = selected.getAttribute('data-path')
        new_path = prompt(__m('_new_filepath'), path);
        if(new_path != null) {
            $.post('fm/move', {newpath:new_path, path:path},function(msg){
                if(msg=='') msg=__m('_file_saved')
                alert(msg);
                g.ajax({url:"admin/media?g_response=content",method:'GET', fn: function (gal){
                    g('#admin-media-div').parent().html(gal)
                }})
            })
        }
    } else {
        alert(__m('_select_file'))
    }
}
function gallery_create(path) {
    path += '/'

    new_path = prompt(__m('_new_folder'), '');
    if(new_path != null) {
        $.post('fm/newfolder', {path:path+new_path},function(msg){
            if(msg=='') msg="File created successfully"
            alert(msg);
            g.ajax({url:"admin/media?g_response=content",method:'GET', fn: function (gal){
                g('#admin-media-div').parent().html(gal)
            }})
        })
    }
}
function gallery_delete_selected() {
    selected = g('.g-selected').all[0]
    if(selected) {
        path = selected.getAttribute('data-path')
        if(path != null) if(confirm("Are you sure you want to remove this file?")) {
            $.post('fm/delete', {path:path},function(msg){
                if(msg=='') msg=__m('_file_deleted')
                alert(msg);
                g.ajax({url:"admin/media?g_response=content",method:'GET', fn: function (gal){
                    g('#admin-media-div').parent().html(gal)
                }})
            })
        }
    } else {
        alert(__m('_select_file'))
    }
}
function gallery_refresh_thumb() {
    selected = g('.g-selected>img').all[0]
    if(selected) {
        path = selected.getAttribute('src')
        if(path != null) {
            $.post('fm/delete', {path:path},function(msg){
                if(msg=='') msg="File thumb updated"
                alert(msg);
                g.ajax({url:"admin/media?g_response=content",method:'GET', fn: function (gal){
                    g('#admin-media-div').parent().html(gal)
                }})
            })
        }
    } else {
        alert(__m('_select_file'))
    }
}

var media_path_input;
g.dialog.buttons.select_media_path = {
    title:'Select',fn:function(){
        let v = g('#selected-path').attr('value')
        if(v!=null) {
            el = g(media_path_input).all[0]
            el.value = v;
            el.dispatchEvent(new Event('input'))
        }
        g('#media_dialog').remove();
    }
}
function open_media_gallery(mpi) {
    media_path_input = mpi;
    g.post("admin/media","g_response=content&path=assets",function(gal){
        g.dialog({title:__m('_gallery'),body:gal,buttons:'select_media_path',id:'media_dialog',class:'large'})
    })
}

function filter_files(query,value) {
 var list = document.querySelectorAll(query)
 list.forEach(function(entry){
  if(!entry.getAttribute('data-path').includes(value))
   entry.style.display='none';
  else
   entry.style.display='inline-block';
 })
}
