<!DOCTYPE html>
<head>
  <base href="<?=Config::base()?>">
  <?php View::$stylesheet = [] ?>
  <?=View::script('core/gila.min.js')?>
  <?=View::script('core/admin/media.js')?>
  <?=View::script('lib/vue/vue.min.js');?>
  <?=View::script('core/admin/content.js')?>
  <?=View::script('core/lang/content/'.Config::get('language').'.js');?>
  <?=View::script("lib/tinymce5/tinymce.min.js")?>
  <?=View::script('core/admin/vue-components.js');?>
  <?=View::script('core/admin/vue-editor.js');?>
  <?=View::cssAsync('lib/font-awesome/css/font-awesome.min.css')?>
  <?=View::cssAsync('core/gila.min.css')?>
  <?=View::cssAsync('core/admin/vue-editor.css');?>
  <?php
  global $db;
  $pageTitle = $db->value("SELECT title FROM `page` WHERE id=?;", $id);
  ?>
  <style>body{padding:0;margin:0;}.fa-d{font-size:120%;margin:auto 5px}
  .g-nav>li>a:hover{background:inherit}.g-nav li a{padding:16px 8px}
  :root{--main-padding:0.5em}
  </style>
</head>
<div style="height:5vh;display:flex;border-bottom:1px solid grey;justify-content: space-between;
align-items: center;padding:0 0.5em;background:#555;color:white" id="editMenu">
  <div style="display:flex;">
    <ul class="g-nav g-navbar" style="background:inherit">
    <li><a href="admin/content/page" style="font-size:180%">
    &lsaquo; <img src="assets/gila-logo.png" style="filter:contrast(0) brightness(2);height:36px;margin:auto;margin-right:24px;margin-left:-8px;vertical-align: middle;">
      </a></li>
    <li class=""><a @click="editPageData()"><i class="fa fa-pencil"></i> {{pageTitle}}</a>
    <li class=""><a @click="toggleEdit()">
      <span v-if="edit=='false'"><i class="fa fa-check-square-o"></i> Preview</span>
      <span v-else><i class="fa fa-square-o"></i> Preview</span>
    </a>
    <li><i class="fa fa-d fa-desktop" onclick="desktopView()"></i>
    <li><i class="fa fa-d fa-tablet" onclick="tabletView()"></i>
    <li><i class="fa fa-d fa-mobile" onclick="mobileView()"></i>
    <li><a @click="askFeedback()" style="margin-left:20px"><i class="fa fa-info-circle"></i> Feedback</a></i>
  </ul>
  </div>

  <div>
    <button v-if="draft=='true'" type="button" class="g-btn btn-white" @click="discardChanges()">Delete Draft</button>
    <button v-if="draft=='true'" type="button" class="g-btn success" @click="saveChanges()">Publish</button>
  </div>

</div>

<div style="display:flex;justify-content:center;height:95vh">
  <iframe style="width:100%;height:95vh;border:0;transition:0.3s;box-shadow:black 0px 0px 5px" id=pageFrame onload="readFrame()"
  src='<?=Config::base()?>blocks/display?t=page&id=<?=htmlentities($id)?>'></iframe>
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
  
var basePageIdUrl = '<?=Config::base()?>blocks/display?t=page&id='+<?=$id?>+'&ts='+Date.now()
appEditMenu = new Vue({
  el:"#editMenu",
  data: {
    draft: false,
    edit: false,
    pageId: <?=$id?>,
    pageTitle: '<?=$pageTitle?>'
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
    editPageData: function() {
      irow = this.pageId
      href='cm/edit_form/page?id='+irow+'&callback=g_page_popup_update';
      g.get(href,function(data){
        g.dialog({title:g.tr('Edit Registry'), class:'lightscreen large',body:data,type:'modal',buttons:'popup_update'})
        formId = '#page-edit-item-form'
        edit_popup_app = new Vue({
          el: formId,
          data: {id:irow}
        })
        transformClassComponents()
        g(formId+' input').all[1].focus()
      })
    },
    askFeedback: function() {
      g.modal({body:'We need your feedback about the page builder so we can make it better for you.<br>Send your questions and thoughts at <strong>contact@gilacms.com</strong>',class:'small'})
    }
  }
})


function g_page_popup_update() {
  form = g('.gila-popup form').last()
  data = new FormData(form);
  t = form.getAttribute('data-table')
  id = form.getAttribute('data-id')
  for(i=0; i<rootVueGTables.length; i++) if(rootVueGTables[i].name == t) {
    _this = rootVueGTables[i]
  }

  url = 'cm/update_rows/'+t+'?id='+id
  g.ajax({method:'post',url:url,data:data,fn:function(data) {
    data = JSON.parse(data)
    appEditMenu.pageTitle = data.rows[0].title
    pageFrame.src = '<?=Config::base()?>blocks/display?t=page&id='+appEditMenu.pageId
  }})

  g.closeModal();
}

var pageDocument=null;

function readFrame() {
    var x = document.getElementById("pageFrame");
    pageDocument = (x.contentWindow || x.contentDocument);
    if (pageDocument.document)pageDocument = pageDocument.document;
    const intervalPageRead = setInterval(function() {
      if(pageDocument.getElementById('draftValue')!==null) {
        appEditMenu.draft = pageDocument.getElementById('draftValue').value
        appEditMenu.edit = pageDocument.getElementById('editValue').value
      }
    }, 200);
}

</script>
