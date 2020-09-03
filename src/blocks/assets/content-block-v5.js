
cmirror=new Array()
mce_editor=new Array()

g.dialog.buttons.update_widget = {title:'Update',fn:function(){
  g.loader()
  fm = new FormData(widget_options_form)
  values = readFromClassComponents()
  for(x in values) {
    fm.set(x, values[x])
  }
  g('#widget-popup').parent().remove();
  g.ajax({url:'blocks/update?g_response=content',method:'POST',data:fm,fn:function(data){
    g.loader(false)
    blocks_preview_reload(data)
    content_blocks_app.draft = true
  }})
}}

g.dialog.buttons.create_widget = {title:'Create',fn:function(){
  g.loader()
  widget_id = cblock_content.replace('/','_')+'_'+cblock_pos;
  g.post('blocks/create', 'id='+widget_id+'&type='+cblock_type, function(data){
    //content_blocks_app.blocks = JSON.parse(data)
    fm = new FormData(widget_options_form)
    values = readFromClassComponents()
    for(x in values) {
      fm.set(x, values[x])
    }
    fm.set('widget_id',widget_id)
    g('#widget-popup').parent().remove();
    g.ajax({url:'blocks/update?g_response=content',method:'POST',data:fm,fn:function(data){
      g.loader(false)
      blocks_preview_reload(data)
      content_blocks_app.draft = true
    }})
  })
}}

g.dialog.buttons.delete_widget = {title:'Delete',class:'error',fn:function() {
  let el = g('#widget_options_form input[name=widget_id]').all[0]
  g('#widget-popup').parent().remove();
  block_del(el.value)
}}

function block_edit_open() {
  app = new Vue({
    el: '#widget_options_form'
  })
  transformClassComponents();
}

function block_edit_close() {
  textareas=g('.codemirror-js').all
  for(i=0;i<textareas.length;i++) {
    textareas[i].value=cmirror[i].getValue()
  }
  textareas_mce=g('.tinymce').all[0]
  if(typeof textareas_mce!='undefined') {
    textareas_mce.value=tinymce.get(mce_editor[0]).getContent()
  }
}

function block_create(content,type,pos) {
  href='blocks/edit?id=new&type='+type;
  _type = type.toUpperCase().replace('_',' ');
  cblock_content=content
  cblock_type=type
  cblock_pos=pos
  g.get(href, function(data) {
    g.dialog({class:'lightscreen large',id:'widget-popup',title:_type,body:data,type:'modal',buttons:'create_widget'})
    block_edit_open()
  });
}

function block_edit(id,type) {
  href='blocks/edit?id='+id+"&type="+type;
  _type = type.toUpperCase().replace('_',' ');
  g.get(href, function(data) {
    g.dialog({class:'lightscreen large',id:'widget-popup',title:_type,body:data,type:'modal',buttons:'update_widget delete_widget'})
    block_edit_open()
  });
};

function block_pos(id,pos) {
  g.loader()
  g.post('blocks/pos', 'id='+id+'&pos='+pos, function(data) {
    g.loader(false)
    blocks_preview_reload(data)
    content_blocks_app.draft = true
  });
}

function block_del(id) {
  if(confirm("You really want to delete this block?")) {
    g.loader()
    g.post('blocks/delete', 'id='+id, function(data) {
      g.loader(false)
      blocks_preview_reload(data)
      content_blocks_app.draft = true
    });
  }
}

function block_add(pos) {
  blocks_app.add_block = true;
  blocks_app.selected_pos = pos;
}

var blocks_app = new Vue({
  el:'#blocks_app',
  data: {
    add_block: false,
    selected_pos: 0,
    blocks: content_blocks
  },
  methods:{
    createBlock: function(content, type, pos) {
      this.add_block = false
      block_create(content,type,pos)
    }
  }
});

function open_gallery_post() {
  g.post("admin/media","g_response=content&path=assets",function(gal){ 
    g.dialog({title:"Media gallery",body:gal,buttons:'select_path_post',class:'large',id:'media_dialog','z-index':99999})
  })
}

blocks_preview_reload = function(data) {
  window.location.reload();
}
