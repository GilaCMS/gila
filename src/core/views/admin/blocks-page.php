
<head>
  <base href="<?=Config::base()?>">
  <?=View::script('core/gila.min.js')?>
  <?=View::script('core/admin/media.js')?>
  <?=View::script('lib/vue/vue.min.js');?>
  <?=View::script('core/lang/content/'.Config::get('language').'.js');?>
  <?=View::script("lib/tinymce5/tinymce.min.js")?>
  <?=View::script('core/admin/vue-components.js');?>
  <?=View::cssAsync('lib/font-awesome/css/font-awesome.min.css')?>
  <?=View::cssAsync('core/gila.min.css')?>
</head>
<style>body{padding:0;margin:0;}</style>
<div style="height:5vh;display:flex;border-bottom:1px solid grey;justify-content: space-between;
align-items: center;padding:0 1em;background:#555;color:white" id="editMenu">
  <span><img src="assets/gila-logo.png" style="filter:contrast(0) brightness(2);width:42px"></span>
  <div style="text-align:center">
    <i class="fa fa-2x fa-desktop" onclick="desktopView()"></i>
    <i class="fa fa-2x fa-tablet" onclick="tabletView()"></i>
    <i class="fa fa-2x fa-mobile" onclick="mobileView()"></i>
  </div>
  <div>
    <button type="button" class="g-btn btn-white" @click="toggleEdit()">
      <span v-if="edit=='false'"><i class="fa fa-pencil"></i></span>
      <span v-else><i class="fa fa-eye"></i></span>
    </button>
    <button v-if="draft=='true'" type="button" class="g-btn btn-white" @click="discardChanges()">Delete Draft</button>
    <button v-if="draft=='true'" type="button" class="g-btn success" @click="saveChanges()">Publish</button>
    <button type="button" class="g-btn" onclick="theme_options('<?=Config::get('theme')?>')"><i class="fa fa-cogs"></i> Options</button>
  </div>
  
  <script>
  function desktopView() {
    pageFrame.style.width = '100%'
    pageFrame.style.height = '95vh'
    pageFrame.style.marginTop = '0'
    pageFrame.style.borderRadius = '0'
    pageFrame.style.border = '0px solid silver';
  }
  function tabletView() {
    pageFrame.style.width = '768px'
    pageFrame.style.height = '90vh'
    pageFrame.style.marginTop = '2em'
    pageFrame.style.borderRadius = '1.5em'
    pageFrame.style.border = '1.5em solid #555';
  }
  function mobileView() {
    pageFrame.style.width = '360px'
    pageFrame.style.marginTop = '6em'
    pageFrame.style.height = '600px'
    pageFrame.style.borderRadius = '1.5em'
    pageFrame.style.border = '1.5em solid #555';
  }
g.dialog.buttons.save_options = {
  title:'<?=__('Save')?>', fn:function() {
    let p = g.el('theme_id').value;
    let fm=new FormData(g.el('theme_options_form'))
    g.loader()
    g.ajax({url:'admin/themes?g_response=content&save_options='+p,method:'POST',data:fm,fn:function(x){
      g.loader(false)
      g('.gila-darkscreen').remove();
      pageFrame.contentWindow.location.reload(true);
    }})
  }
}

function theme_options(p) {
  g.loader()
  g.post("admin/themes?g_response=content", 'options='+p,function(x){
    g.loader(false)
    g.modal({title:"<?=__('Options')?>",body:x,buttons:'save_options',type:'modal'})
    app = new Vue({
      el: '#theme_options_form'
    })
  })
}
  </script>
</div>

<div style="background:#eee;display:flex;justify-content:center;height:95vh">
  <iframe style="width:100%;height:95vh;border:0;transition:0.3s;box-shadow:black 0px 0px 5px" id=pageFrame onload="readFrame()" src='<?=Config::base()?>blocks/display?t=page&id=<?=htmlentities($id)?>'></iframe>
</div>

<script>

appEditMenu = new Vue({
  el:"#editMenu",
  data: {
    draft: false,
    edit: false
  },
  methods: {
    discardChanges: function(){
      pageDocument.getElementById('discardChanges').click()
    },
    saveChanges: function(){
      pageDocument.getElementById('saveChanges').click()
    },
    toggleEdit: function(){
      pageDocument.getElementById('swapEdit').click()
    }
  }
})

var pageDocument=null;

function readFrame() {
    var x = document.getElementById("pageFrame");
    pageDocument = (x.contentWindow || x.contentDocument);
    if (pageDocument.document)pageDocument = pageDocument.document;
    const intervalPageRead = setInterval(function() {
      appEditMenu.draft = pageDocument.getElementById('draftValue').value
      appEditMenu.edit = pageDocument.getElementById('editValue').value
    }, 100);
}



</script>
