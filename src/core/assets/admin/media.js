//#main-wrapper
g.click(".gal-image",function(){
    g('.gal-path').removeClass('g-selected');
    g(this).addClass('g-selected');
    g('#selected-path').attr('value',this.getAttribute('data-path'))
    g('#selected-image-caption').attr('value',this.getAttribute('data-caption'))
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
    refresh_media_body("path="+path);
})
function refresh_media_body(data) {
  g.post("admin/media?g_response=content", data, function(gal){
    g('#admin-media-div').parent().html(gal)
  })
}
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
  let fm=new FormData()
  fm.append('uploadfiles', g.el('upload_files').files[0]);
  fm.append('formToken', g.el('upload_files').getAttribute('data-csrf'));
  fm.append('path', g.el('upload_files').getAttribute('data-path'));
  fm.append('g_response', 'content');
  g.loader()
  g.ajax({url:"admin/media_upload",method:'POST',data:fm, fn: function (gal){
    g.loader(false)
    g('#admin-media-div').parent().html(gal)
  }})
}
function gallery_move_selected(path) {
    selected = g('.g-selected').all[0]
    if(selected) {
        old_path = selected.getAttribute('data-path')
        new_path = prompt(__m('_new_filepath'), old_path);
        if(new_path != null) {
          csrfToken=g.el('upload_files').getAttribute('data-csrf')
          $.post('fm/move', 'newpath='+new_path+'&path='+old_path+'&formToken='+csrfToken, function(msg){
              if(msg=='') msg=__m('_file_saved')
              alert(msg);
              update_gallery_body(path);
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
    g.loader()
    csrfToken=g.el('upload_files').getAttribute('data-csrf')
    g.post('fm/newfolder', 'path='+path+new_path+'&formToken='+csrfToken,function(msg){
      g.loader(false)
      if(msg=='') msg="File created successfully"
      alert(msg);
      update_gallery_body(path);
    })
  }
}

function gallery_delete_selected(path) {
  selected = g('.g-selected').all[0]
  if(selected) {
      filepath = selected.getAttribute('data-path')
      if(filepath != null) if(confirm("Are you sure you want to remove this file?")) {
        g.loader()
        csrfToken=g.el('upload_files').getAttribute('data-csrf')
        g.post('fm/delete', 'path='+filepath+'&formToken='+csrfToken,function(msg){
          g.loader(false)
          if(msg=='') msg=__m('_file_deleted')
          alert(msg);
          update_gallery_body(path);
        })
      }
  } else {
    alert(__m('_select_file'))
  }
}

function gallery_refresh_thumb(path) {
  selected = g('.g-selected>img').all[0]
  if(selected) {
    filepath = selected.getAttribute('src')
    if(filepath != null) {
      g.loader()
      csrfToken=g.el('upload_files').getAttribute('data-csrf')
      g.post('fm/delete', 'path='+filepath+'&formToken='+csrfToken,function(msg){
        g.loader(false)
        if(msg=='') msg="File thumb updated"
        alert(msg);
        update_gallery_body(path);
      })
    }
  } else {
    alert(__m('_select_file'))
  }
}

var media_path_input;
var media_image_caption_input;
g.dialog.buttons.select_media_path = {
  title:'Select',fn:function(){
    let v = g('#selected-path').attr('value')
    let c = g('#selected-image-caption').attr('value')
    g('#media_dialog').parent().remove();
    if(v!=null) {
      elem = g(media_path_input).all[0]
      elem.value = v;
      elem.dispatchEvent(new Event('input'))
      list = g(media_image_caption_input)
      if(typeof list!=='undefined') {
        elem = list.all[0]
        elem.value = c;
      }
    }
  }
}
function open_media_gallery(mpi, mici) {
  media_path_input = mpi;
  media_image_caption_input = mici;
  g.post("admin/media","g_response=content",function(gal){
        g.dialog({title:__m('_gallery'),body:gal,buttons:'select_media_path',type:'modal',id:'media_dialog',class:'large'})
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

function update_gallery_body(path) {
  g.ajax({url:"admin/media?g_response=content&path="+path,method:'GET', fn: function (gal){
    g('#admin-media-div').parent().html(gal)
  }})
}

g.click('.media-tabs-side>div', function(e){
  g(this).parent().children().style('opacity', 0.3)
  this.style.opacity=1;
  g.post("admin/media?media_tab="+g(this).attr('data-tab'), "g_response=content",function(gal){
    g('#media_dialog .body').html(gal)
  })
});
