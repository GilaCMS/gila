<?=View::script('core/gila.min.js')?>
<?=View::script('lib/CodeMirror/codemirror.js')?>
<?=View::script('lib/CodeMirror/javascript.js')?>
<?=View::script('lib/vue/vue.min.js');?>

<?=View::script("lib/tinymce5/tinymce.min.js")?>
<?=View::script('core/admin/vue-components.js');?>
<?=View::script('core/admin/media.js')?>
<?=View::script('core/lang/content/'.Config::config('language').'.js')?>

<?=View::css('lib/font-awesome/css/font-awesome.min.css')?>
<?=View::css('blocks/blocks.css')?>
<?=View::cssAsync('core/gila.min.css')?>
<?=View::cssAsync('lib/CodeMirror/codemirror.css')?>

<?php
$cid = $content.'_'.$id.'_';
?>

<div id="content_blocks_list" style="position:absolute;right:1em;top:1em;padding:0;">
  <div v-if="draft" style="margin-left:2em">
    <button class='g-btn success' @click='block_save()'><?='<i class="fa fa-save"></i> '.__('Save')?></button>
    <?=(isset($title)?'&nbsp;':'')?>
    <button class='g-btn-white' @click='block_discard()'><?='<i class="fa fa-trash"></i> '.__('Discard Draft')?> </button>
    <br>
  </div>
</div>

<style>
.block-head:hover + *, .block-head + *:hover {
  border:1px dashed grey;
}
.block-edit-btn,.block-add-btn,.block-switch-btn,.block-del-btn{
  padding:6px;
  border-radius:12px;
  font:14px Arial;
  font-weight:bold;
  background:#ccc;
  opacity:0.66;
  color:#000;
}
.block-edit-btn:hover,.block-add-btn:hover,.block-switch-btn:hover,.block-del-btn:hover{
  opacity:1;
}
#content_blocks_list button{
  border-radius:3em;
  box-shadow:0 0 2px grey;
  opacity:0.9;
  font-family:Arial;
}
#content_blocks_list button:hover{
  opacity:1;
  box-shadow:0 0 3px grey;
}
</style>

<script>
g('.block-head').html("<div style='position:relative;width:100%;z-index:1'>\
<span style='position:absolute;left:0;top:0.5em'><button class='block-edit-btn'>EDIT</button></span>\
<span style='position:absolute;left:45%;top:-1em'><button class='block-add-btn'>+ ADD BLOCK</button></span>\
<span style='position:absolute;right:58%;top:-1em'><button class='block-switch-btn'>&nbsp;<i class='fa fa-arrows-v'></i>&nbsp;</button></span>\
<span style='position:absolute;right:0;top:0.5em'><button class='block-del-btn'><i class='fa fa-trash'></i></button></span>\
</div>");

g.click('.block-edit-btn', function(){
  pos = this.parentNode.parentNode.parentNode.getAttribute('data-pos')
  type = this.parentNode.parentNode.parentNode.getAttribute('data-type')
  block_edit("<?=$cid?>"+pos, type)
})

g.click('.block-add-btn', function(){
  pos = this.parentNode.parentNode.parentNode.getAttribute('data-pos')
  blocks_app.add_block = true;
  blocks_app.selected_pos = pos;
})

g.click('.block-switch-btn', function(){
  pos = this.parentNode.parentNode.parentNode.getAttribute('data-pos')
  block_pos("<?=$cid?>"+pos,pos-1)
})

g.click('.block-del-btn', function(){
  pos = this.parentNode.parentNode.parentNode.getAttribute('data-pos')
  fast_block_del("<?=$cid?>"+pos)
})

g('.block-end').html("<div style='position:relative;width:100%;'>\
<span style='position:absolute;left:51%;top:-1em'><button class='block-add-btn'>+ ADD BLOCK</button></span>\
</div>");


<?php
$content_blocks = [];

foreach (Config::$widget as $k=>$w) {
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

function getElementIndex (element) {
  return Array.from(element.parentNode.children).indexOf(element);
}

content_blocks_app = new Vue({
  el: "#content_blocks_list",
  data: {
    blocks: <?=json_encode($widgets??[])?>,
    draft: <?=($isDraft?'true':'false')?>
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
    }
  }
})

</script>

<div id='blocks_app'>
  <div v-if="add_block" id="add_block">
    <img src="assets/core/admin/close.svg" class="add-block-x" @click="add_block=false">
    <div class="add-block-grid centered">
      <div class="add-block-btn" v-for="b in blocks" @click="createBlock('<?=$content.'/'.$id?>', b.name, selected_pos)">
        <img v-if="b.preview" :src="'lzld/thumb?media_thumb=300&src=src/'+b.preview" class="preview">
        <div v-else class="logo">
          <img v-if="b.logo" :src="'lzld/thumb?src=src/'+b.logo">
          <h4 v-else>{{b.name[0].toUpperCase()}}</h4>
          <div>{{b.name}}<div>
        </div>
      </div>
    </div>
  </div>
</div>

<?=View::script("core/admin/content.js")?>
<?=View::script("blocks/content-block-v5.js")?>
