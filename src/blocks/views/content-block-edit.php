<?=View::script('core/gila.min.js')?>
<?=View::script('lib/CodeMirror/codemirror.js')?>
<?=View::script('lib/CodeMirror/javascript.js')?>
<?=View::script('lib/vue/vue.min.js');?>

<?=View::script("lib/tinymce5/tinymce.min.js")?>
<?=View::script('core/admin/vue-components.js');?>
<?=View::script('core/admin/vue-editor.js');?>
<?=View::script('core/admin/media.js')?>
<?=View::script('core/lang/content/'.Config::config('language').'.js')?>

<?=View::css('lib/font-awesome/css/font-awesome.min.css')?>
<?=View::css('blocks/blocks.css')?>
<?=View::cssAsync('core/gila.min.css')?>
<?=View::cssAsync('core/admin/vue-editor.css')?>
<?=View::cssAsync('lib/CodeMirror/codemirror.css')?>

<?php
$cid = $content.'_'.$id.'_';
?>

<script>
g('.block-head').html("<div style='position:relative;width:100%;'>\
<span style='position:absolute;left:0;top:2em'><button class='block-edit-btn'>EDIT</button></span>\
<span style='position:absolute;left:51%;top:-1em'><button class='block-add-btn'>+ ADD BLOCK</button></span>\
<span style='position:absolute;right:51%;top:-1em'><button class='block-switch-btn'>SWITCH POS</button></span>\
<!--span style='position:absolute;right:0;top:2em'><button>DELETE</button></span-->\
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
<?=View::script("blocks/content-block-v2.js")?>
