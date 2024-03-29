
g.click(".gal-image",function(){
  select_media_item(this)
})
g.click(".gal-folder",function(){
  if(g(this).hasClass('g-selected')==false) {
    select_media_item(this)
  } else {
    let path=this.getAttribute('data-path')
    g.ajax({url:"admin/media",method:"POST",header:"application/x-www-form-urlencoded",data:"g_response=content&path="+path,fn:function(gal){ //
        g('#admin-media-div').parent().html(gal)
    }})
  }
})
g.click("#fm-goup",function(){
  if(this.getAttribute('data-path')=='') return;
  let path=this.getAttribute('data-path')
  refresh_media_body("path="+path);
})
function select_media_item(item) {
  g('.gal-path').removeClass('g-selected');
  g(item).addClass('g-selected');
  g('#selected-path').attr('value', item.getAttribute('data-path'))
  g('#selected-image-caption').attr('value', item.getAttribute('data-caption'))
}
function refresh_media_body(data, tab=null) {
  url = "admin/media?g_response=content"
  if (tab!==null) {
    url = url + '&media_tab='+tab
  }
  g.post(url, data, function(gal){
    g('#admin-media-div').parent().html(gal)
  })
}

// Translation
function _e(phrase)
{
  if(typeof lang_array!='undefined') if(typeof lang_array[phrase]!='undefined') return lang_array[phrase];
  return phrase;
}

function gallery_upload_files() {
  let fm=new FormData()
  for(i in g.el('upload_files').files) if(!isNaN(i)) {
    fm.append('uploadfiles['+i+']', g.el('upload_files').files[i]);
  }
  fm.append('formToken', g.el('upload_files').getAttribute('data-csrf'));
  fm.append('path', g.el('upload_files').getAttribute('data-path'));
  fm.append('g_response', 'content');
  g.loader()
  g.ajax({url:"admin/media_upload",method:'POST',data:fm, fn: function (gal){
    g.loader(false)
    g('#admin-media-div').parent().html(gal)
  }})
}
function gallery_drop_files(e) {
  let fm=new FormData()
  e.stopPropagation(); // Stops some browsers from redirecting.
  var files = e.dataTransfer.files;
  e.preventDefault();
  for (var i = 0, f; f = files[i]; i++) {
    fm.append('uploadfiles['+i+']', f);
  }

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
        new_path = prompt(g.tr('_new_filepath'), old_path);
        if(new_path != null) {
          csrfToken=g.el('upload_files').getAttribute('data-csrf')
          g.post('fm/move', {newpath:new_path, path:old_path, formToken:csrfToken}, function(msg){
              if(msg=='') msg=g.tr('_file_saved')
              alert(msg);
              update_gallery_body(path);
          })
        }
    } else {
        alert(g.tr('_select_file'))
    }
}

function gallery_create(path) {
  path += '/'
  new_path = prompt(g.tr('_new_folder'), '');
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
          if(msg!=='') alert(msg);
          update_gallery_body(path);
        })
      }
  } else {
    alert(g.tr('_select_file'))
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
    alert(g.tr('_select_file'))
  }
}

var media_path_input;
var media_image_caption_input;
g.dialog.buttons.select_media_path = {
  title:'Select',fn:function(){
    let v = g('#selected-path').attr('value')
    let c = g('#selected-image-caption').attr('value')
    g.closeModal('media_dialog');
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

var open_media_gallery_clicked = false
function open_media_gallery(mpi, mici) {
  media_path_input = mpi;
  media_image_caption_input = mici;
  if(open_media_gallery_clicked) return;
  open_media_gallery_clicked = true;
  g.post("admin/media","g_response=content",function(gal){
    open_media_gallery_clicked = false;
    g.dialog({title:g.tr('_gallery'),body:gal,buttons:'select_media_path',type:'modal',id:'media_dialog',class:'large'})
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

g.click('.media-tabs-side>div', function(){
  g(this).parent().children().style('opacity', 0.3)
  this.style.opacity=1;
  g.post("admin/media?media_tab="+g(this).attr('data-tab'), "g_response=content",function(gal){
    g('#media_dialog .body').html(gal)
  })
});
