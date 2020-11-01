<?=View::script('core/gila.min.js')?>
<?=View::script('lib/CodeMirror/codemirror.js')?>
<?=View::script('lib/CodeMirror/javascript.js')?>
<?=View::script('lib/vue/vue.min.js');?>

<?=View::script("lib/tinymce5/tinymce.min.js")?>
<?=View::script('core/admin/vue-components.js');?>
<?=View::script('core/admin/media.js')?>
<?=View::script('core/lang/content/'.Config::config('language').'.js')?>

<?=View::css('lib/font-awesome/css/font-awesome.min.css')?>
<?=View::cssAsync('core/admin/blocks.css')?>
<?=View::cssAsync('core/gila.min.css')?>
<?=View::cssAsync('lib/CodeMirror/codemirror.css')?>
<?=View::script("core/admin/content.js")?>

<?php
$cid = $content.'_'.$id.'_';
?>

<div id="content_blocks_list" style="position:fixed;right:2em;top:0.5em;padding:0;z-index:999" :class="{opacity05:load==true}">
  <div style="margin-left:2em">
    <button v-if="draft" class='g-btn success content_blocks_btn' @click='block_save()'><?='<i class="fa fa-check"></i> '.__('Save')?></button>
    <?=(isset($title)?'&nbsp;':'')?>
    <button v-if="draft" class='g-btn-white content_blocks_btn' @click='block_discard()'><?='<i class="fa fa-trash"></i> '.__('Discard Draft')?> </button>
    <?=(isset($title)?'&nbsp;':'')?>
    <button class='g-btn-white content_blocks_btn' @click='switch_edit()'> &nbsp;<i class="fa fa-pencil"></i>&nbsp; </button>
    <br>
  </div>
</div>

<style>
.block-head {
  min-height:2em;
}
.block-head:hover {
  /*border:1px dashed steelblue;*/
}
.opacity05{
  opacity:0.5;
}
.block-edit-btn,.block-add-btn,.block-swap-btn,.block-del-btn{
  padding:6px;
  border-radius:14px;
  font:14px Arial;
  font-weight:bold;
  background:steelblue;
  opacity:0.8;
  color:white;
  border:1px solid steelblue;
  min-width:28px;
}
.block-head:nth-child(1) .block-swap-btn{
  display:none;
}
.block-head>div:nth-child(1){
  position:relative;width:100%;
}
.block-head:nth-child(1) .span-add-btn{
  top:0;
}
.block-head>div,.block-end>div{
  z-index:11;
}
.hide{
  display:none;
}
.block-edit-btn:hover,.block-add-btn:hover,.block-swap-btn:hover,.block-del-btn:hover{
  opacity:1;
  color:white;
}
.content_blocks_btn{
  border-radius:3em;
  box-shadow:0 0 2px grey;
  opacity:0.9;
  font-family:Arial;
}
.content_blocks_btn:hover{
  opacity:1;
  box-shadow:0 0 3px grey;
}
.span-add-btn{
  position:absolute;left:45%;top:-1em
}
.span-edit-btn{
  position:absolute;left:4px;top:0.5em
}
.span-swap-btn{
  position:absolute;right:58%;top:-1em
}
.span-del-btn{
  position:absolute;right:4px;top:0.5em
}
@media only screen and (max-width:400px){
  #content_blocks_list, .block-head>div:nth-child(1), .block-end {
    display:none;
  }
}
</style>

<script>
g('.block-head').prepend("<div>\
<span class='span-edit-btn'><button class='block-edit-btn'>EDIT</button></span>\
<span class='span-add-btn'><button class='block-add-btn'>+ ADD BLOCK</button></span>\
<span class='span-swap-btn'><button class='block-swap-btn'>&nbsp;<i class='fa fa-arrows-v'></i>&nbsp;</button></span>\
<span class='span-del-btn'><button class='block-del-btn'><i class='fa fa-trash'></i></button></span>\
</div>");

let inlineTinies=g('.inline-tinymce').all
let inlineTexts=g('[data-inline]').all
let inlineTextValues=new Array(inlineTexts.length)
for(i=0; i<inlineTexts.length; i++) {
  inlineTextValues[i] = inlineTexts[i].innerHTML
}

document.addEventListener("click", function(e){
  if(g(e.target).findUp('#widget_options_form').all==null) {
    e.preventDefault();
  }
});
document.addEventListener("keyup", function(e){
  args=[]
  for(i=0; i<inlineTexts.length; i++) if(inlineTextValues[i]!=inlineTexts[i].innerHTML){
    inlineTextValues[i]=inlineTexts[i].innerHTML
    key = inlineTexts[i].getAttribute('data-inline')
    args[key] = inlineTexts[i].innerHTML
  }
});

g.click('.block-edit-btn', function(){
  pos = this.parentNode.parentNode.parentNode.getAttribute('data-pos')
  type = this.parentNode.parentNode.parentNode.getAttribute('data-type')
  block_edit("<?=$cid?>"+pos, type)
})

g.click('.block-add-btn', function(){
  pos = this.parentNode.parentNode.parentNode.getAttribute('data-pos')
  blocks_app.openList();
  blocks_app.selected_pos = pos;
})

g.click('.block-swap-btn', function(){
  _block = this.parentNode.parentNode.parentNode
  pos = _block.getAttribute('data-pos')-1
  id = "<?=$cid?>"+(pos+1)
  content_blocks_app.loader()
  g.post('blocks/pos', 'id='+id+'&pos='+pos, function(data) {
    content_blocks_app.loader(false)
    if (_block.previousSibling) {
      _block.parentNode.insertBefore(_block, _block.previousSibling);
    }
    content_blocks_app.draft = true
    _block.scrollIntoView(); 
  });

})

g.click('.block-del-btn', function(){
  _block = this.parentNode.parentNode.parentNode
  pos = _block.getAttribute('data-pos')
  id = "<?=$cid?>"+pos
  content_blocks_app.loader()
  g.post('blocks/delete', 'id='+id, function(data) {
    content_blocks_app.loader(false)
    _block.parentNode.removeChild(_block)
    content_blocks_app.draft = true
  });
})

g('.block-end').html("<div style='position:relative;width:100%;'>\
<span style='position:absolute;left:45%;'><button class='block-add-btn'>+ ADD BLOCK</button></span>\
</div>");


<?php
$content_blocks = [];

foreach (Widget::getList('page') as $k=>$w) {
  $c = ['name'=>$k];
  if (file_exists('src/'.$w.'/logo.png')) {
    $c['logo'] = $w.'/logo.png';
  }
  if (file_exists('src/'.$w.'/logo.svg')) {
    $c['logo'] = $w.'/logo.svg';
  }
  if (file_exists('src/'.$w.'/preview.png')) {
    $c['preview'] = $w.'/preview.png';
  }
  $content_blocks[$k] = $c;
}
?>

content_blocks = <?=json_encode($content_blocks)?>;
base_url = "<?=Config::config('base')?>"
g_tinymce_options.document_base_url = "<?=Config::config('base')?>"


function getElementIndex (element) {
  return Array.from(element.parentNode.children).indexOf(element);
}

/*********
g_tinymce_options = {
  selector: '',
  relative_urls: false,
  remove_script_host: false,
  height: 300,
  remove_linebreaks : false,
  document_base_url: ".",
  verify_html: false,
  cleanup: true,
  plugins: ['code codesample table charmap image media lists link emoticons'],
  menubar: true,
  entity_encoding: 'raw',
  toolbar: 'formatselect bold italic | bullist numlist outdent indent | link image table | alignleft aligncenter alignright alignjustify',
  file_picker_callback: function(cb, value, meta) {
    input_filename = cb;
    open_gallery_post();
  },
  inline:true
}

let inlinetinies
inlinetinies=g('.inline-tinymce').all
mce_editor=[]
if(tinymce) tinymce.remove() //remove all tinymce editors
for(i=0;i<inlinetinies.length;i++) {
  mce_editor[i] = {id: inlinetinies[i].id};
  mce_editor[i].settings = JSON.parse(JSON.stringify(g_tinymce_options));
  mce_editor[i].settings.selector = '[id='+inlinetinies[i].id.replace('[','\\[').replace(']','\\]')+']'
  mce_editor[i].settings.file_picker_callback = function(cb, value, meta) {
    input_filename = cb;
    open_gallery_post();
  }
  tinymce.init(mce_editor[i].settings)
}
********/


content_blocks_app = new Vue({
  el: "#content_blocks_list",
  data: {
    blocks: <?=json_encode($widgets??[])?>,
    draft: <?=($isDraft?'true':'false')?>,
    load: false
  },
  methods: {
    block_save: function() {
      _this = this
      g.loader()
      g.post('blocks/save', 'id=<?=$content.'_'.$id?>', function(data) {
        g.loader(false)
        _this.draft = false;
        g.alert("Saved", "success");
      });
    },
    block_discard: function() {
      _this = this
      g.loader()
      g.post('blocks/discard', 'id=<?=$content.'_'.$id?>', function(data) {
        g.loader(false)
        _this.draft = false;
        blocks_preview_reload(data)
      });
    },
    switch_edit: function() {
      g('.block-head>div:nth-child(1)').toggleClass('hide');
      g('.block-end>div:nth-child(1)').toggleClass('hide');
    },
    loader: function(x=true) {
      g.loader(x)
      this.load = x
    }
  }
});

</script>

<div id='blocks_app'>
  <div id="add_block" v-if="add_block" ref="blocks_app" style="transition:0.3s">
    <div style="position:fixed;left:0;right:0;top:0;bottom:0;background:rgba(0,0,0,0.5);z-index:-100" @click="closeList()"></div>
    <div style="text-align:center;background:white;display:flex">
      &nbsp;<input type="text" class="g-input" style="max-width:220px;margin-top:10px" v-model="filter" ref="filter">
      <img src="assets/core/admin/close.svg" class="add-block-x" @click="closeList()">
    </div>
    <div class="add-block-grid" style="margin:auto;width:100%;grid-gap:0;height:100%;max-height:100%;background:#f4f4ff;">
      <div class="add-block-btn" v-for="b in blocks" v-if="b.visible!==false" @click="createBlock('<?=$content.'/'.$id?>', b.name, selected_pos)">
        <img v-if="b.preview" :src="'lzld/thumb?media_thumb=300&src=src/'+b.preview" class="preview" :title="b.name">
        <div v-else class="logo">
          <img v-if="b.logo" :src="'lzld/thumb?src=src/'+b.logo">
          <b v-else>{{b.name[0].toUpperCase()}} </b>
          <span>{{b.name}}<span>
        </div>
      </div>
    </div>
  </div>
</div>

<?=View::script("core/admin/content-block.js")?>
