<!DOCTYPE html>
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
  <?php
  global $db;
  $templates = View::getTemplates('page');
  $pageTemplates = [''=>'[Default]'];
  foreach ($templates as $template) {
    $pageTemplates[$template] = ucwords($template);
  }
  $pages = [];
  $res = $db->get("SELECT id,title,slug,publish FROM `page`;");
  foreach ($res as $r) {
    $pages[$r['id']] = $r['title'];
  }
  ?>
  <style>body{padding:0;margin:0;}.fa-d{font-size:120%;margin:auto 5px}</style>
</head>
<div style="height:5vh;display:flex;border-bottom:1px solid grey;justify-content: space-between;
align-items: center;padding:0 1em;background:#555;color:white" id="editMenu">
  <div style="display:flex">
    <div><img src="assets/gila-logo.png" style="filter:contrast(0) brightness(2);width:42px;margin:auto 2em"></div>
    <ul class="g-nav g-navbar" style="background:inherit">
    <li class="dropdown" id="pagesDropdown"><a>Page</a>
      <ul class="dropdown-menu"><li v-for="(page,i) in pages" @click="selectPage(i)"><a>{{page}}</a></li></ul>
    <li class="dropdown" id="layoutsDropdown"><a>Layout</a>
      <ul class="dropdown-menu"><li v-for="(layout,i) in layouts" @click="previewLayout(i)"><a>{{layout}}</a></li></ul>
    <li class=""><a onclick="alert('still not working!')"><i class="fa fa-pencil"></i> Edit</a>
    <li class="dropdown" id="themesDropdown"><a>Theme</a>
      <ul class="dropdown-menu"><li v-for="(theme,i) in themes" @click="previewTheme(i)">
        <a>{{theme}}<span v-if="i==currentTheme"> &check;</span></a>
      </li></ul>
    <li class=""><a onclick="theme_options('<?=Config::get('theme')?>')"><i class="fa fa-cog"></i> Options</a>
    <li class=""><a @click="toggleEdit()">
      <span v-if="edit=='false'"><i class="fa fa-check-square-o"></i> Preview</span>
      <span v-else><i class="fa fa-square-o"></i> Preview</span>
    </a>
    <li><i class="fa fa-d fa-desktop" onclick="desktopView()"></i>
    <li><i class="fa fa-d fa-tablet" onclick="tabletView()"></i>
    <li><i class="fa fa-d fa-mobile" onclick="mobileView()"></i>
  </ul>
  </div>
  <div v-if="previewedTheme">
    Keep Theme?
    <button type="button" class="g-btn success" @click="selectPreviewTheme()"><?=__('Yes')?></button>
    <button type="button" class="g-btn warning" @click="removePreviewTheme()"><?=__('No')?></button>
  </div>
  <div v-if="previewedLayout!==null">
    Keep Layout?
    <button type="button" class="g-btn success" @click="selectPreviewLayout()"><?=__('Yes')?></button>
    <button type="button" class="g-btn warning" @click="removePreviewLayout()"><?=__('No')?></button>
  </div>
  <div>
    <button v-if="draft=='true'" type="button" class="g-btn btn-white" @click="discardChanges()">Delete Draft</button>
    <button v-if="draft=='true'" type="button" class="g-btn success" @click="saveChanges()">Publish</button>
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
  <iframe style="width:100%;height:95vh;border:0;transition:0.3s;box-shadow:black 0px 0px 5px" id=pageFrame onload="readFrame()"
  src='<?=Config::base()?>blocks/display?t=page&id=<?=htmlentities($id)?>'></iframe>
</div>

<script>

appEditMenu = new Vue({
  el:"#editMenu",
  data: {
    draft: false,
    edit: false,
    pageId: <?=$id?>,
    currentTheme: '<?=Config::get('theme')?>',
    previewedTheme: null,
    previewedLayout: null,
    pages: <?=json_encode($pages)?>,
    layouts: <?=json_encode($pageTemplates)?>,
    themes: {'gila-mag':'Theme1','gila-blog':'Blog','unique':'Unique'}
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
    },
    selectPage: function(i) {
      pageFrame.src = '<?=Config::base()?>blocks/display?t=page&id='+i
      this.pageId = i
      pagesDropdown.classList.toggle('open')
    },
    previewTheme: function(i) {
      this.previewedLayout = null
      if(this.currentTheme==i) {
        this.previewedTheme = null
        pageFrame.src = '<?=Config::base()?>blocks/display?t=page&id='+this.pageId+'&g_preview_theme='+i
      } else {
        this.previewedTheme = i
        pageFrame.src = '<?=Config::base()?>blocks/display?t=page&id='+this.pageId+'&g_preview_theme='+i
      }
      themesDropdown.classList.toggle('open')
    },
    removePreviewTheme: function() {
      pageFrame.src = '<?=Config::base()?>blocks/display?t=page&id='+this.pageId
      this.previewedTheme = null
    },
    selectPreviewTheme: function() {
      // select theme
      g.loader()
      alert('still not working!')
      //g.post('admin/themes?g_response=content', 'activate='+this.previewedTheme,function(x){
      //  g.loader(false)
      //  g.alert("<?=__('_theme_selected')?>",'success','location.reload(true)');
      //})
      this.previewedTheme = null
    },
    previewLayout: function(i) {
      this.previewedTheme = null
      this.previewedLayout = i
      pageFrame.src = '<?=Config::base()?>blocks/display?t=page&id='+this.pageId+'&g_preview_template='+i
      layoutsDropdown.classList.toggle('open')
    },
    removePreviewLayout: function() {
      pageFrame.src = '<?=Config::base()?>blocks/display?t=page&id='+this.pageId
      this.previewedLayout = null
    },
    selectPreviewLayout: function() {
      // select layout
      alert('still not working!')
      this.previewedLayout = null
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