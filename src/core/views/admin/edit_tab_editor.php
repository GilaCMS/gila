<link rel="stylesheet" href="lib/quill/quill.snow.css" />
<script type="text/javascript" src="lib/quill/quill.min.js"></script>

<script src="lib/CodeMirror/codemirror.js"></script>
<link rel="stylesheet" href="lib/CodeMirror/codemirror.css">
<script src="lib/CodeMirror/mode/javascript/javascript.js"></script>
<script src="lib/CodeMirror/mode/css/css.js"></script>
<script src="lib/CodeMirror/mode/xml/xml.js"></script>
<script src="lib/CodeMirror/mode/php/php.js"></script>
<script src="lib/CodeMirror/mode/htmlmixed/htmlmixed.js"></script>
<script src="lib/CodeMirror/formatting.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.6.14/beautify.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.6.14/beautify-css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.6.14/beautify-html.min.js"></script>
<script src='http://lovasoa.github.io/tidy-html5/tidy.js'></script>

<ul class="g-nav g-tabs gs-12">
    <li><a data-href="#quill-tab">Quill</a></li>
    <li><a data-href="#code-tab">Code</a></li>
</ul>
<div class="tab-content gs-12">
    <div id='quill-tab' class="gs-12 quill-tab">
        <?php include __DIR__.'/edit_quill_bar.php'; ?>
        <div id="quill-container" style="width:100%;background:white;height:350px"><?=nl2br(isset($p->page)?$p->page:$p->post)?></div>
    </div>
    <div id="code-tab"  class="gs-12" style="height:350px">
        <textarea><?=nl2br(isset($p->page)?$p->page:$p->post)?></textarea>
    </div>
</div>
<script>
//import jsbeautifier
var code_editor_use = false;

g.click('.g-tabs a',function(){

    g(event.target).findUp('.g-tabs').children().removeClass('active');
    g(event.target).findUp('li').addClass('active');

    hash=event.target.getAttribute('data-href').split('#');
    if(typeof hash[1]!=='undefined') if(hash[1]!==''){
        x='#'+hash[1];
        g(x).parent().children().style('display','none');
        g(x).style('display','block');


        if(code_editor_use) if(hash[1]=='quill-tab') {
            code_editor_use = false;
            g.el('quill-container').innerHTML=mirror.getValue(); //g('#code-tab>textarea').all[0].value;
           quill = new Quill('#quill-container', {
              modules: {
                toolbar: '#quill-toolbar-container'
              },
              theme: 'snow'
          });
        }
        if(!code_editor_use) if(hash[1]=='code-tab') {
            code_editor_use = true;
            //g('#code-tab').all[0].innerHTML = '<textarea></textarea>';

              options = {
              "indent":"auto",
              "indent-spaces":2,
              //"wrap":80,
              "markup":true,
              "output-xml":false,
              "numeric-entities":true,
              "quote-marks":true,
              "quote-nbsp":false,
              "show-body-only":true,
              "quote-ampersand":false,
              "break-before-br":true,
              "uppercase-tags":false,
              "uppercase-attributes":false,
              "drop-font-tags":true,
              "tidy-mark":false
            }

            //mirror.setValue(tidy_html5(quill.root.innerHTML));
            mirror.setValue(quill.root.innerHTML);
            CodeMirror.commands["selectAll"](mirror);
mirror.autoFormatRange(mirror.getCursor(true), mirror.getCursor(false));
mirror.setCursor(0);

            //g.el('quill-container').innerHTML = '<textarea>'+quill.root.innerHTML+'</textarea>'
            //g('#code-tab>textarea').all[0].innerHTML = g('#quill-container .ql-editor').all[0].innerHTML;
            //mirror = CodeMirror.fromTextArea(g('#code-tab textarea').all[0],{lineWrapping:true,lineNumbers:true,mode:'htmlmixed'});
        }

    }
    return false;
})
var quill = new Quill('#quill-container', {
    modules: {
        //formula: true,
        //syntax: true,
        toolbar: '#quill-toolbar-container'
    },
    placeholder: 'Compose an epic...',
    theme: 'snow'
});
var mirror = CodeMirror.fromTextArea(g('#code-tab>textarea').all[0],{lineWrapping:true,lineNumbers:true,mode:'htmlmixed'});

function get_text_to_save() {
    if(code_editor_use) return mirror.getValue(); //g('#code-tab>textarea').all[0].innerHTML;
    return g('#quill-container .ql-editor').all[0].innerHTML;
}
</script>

<!--script src="lib/tinymce/js/tinymce/tinymce.min.js"></script-->
<script>
//cdn.tinymce.com/4/tinymce.min.js
//tinymce.init({ selector:'textarea' });
/*
tinymce.init({
  selector: '[name=p_post]',
  mode: 'none',
  height: 300,
  //theme: 'modern',
  plugins: [
    'advlist autolink lists link image charmap print  hr anchor pagebreak',
    'searchreplace wordcount  visualchars code ',
    'insertdatetime media nonbreaking table contextmenu ', //directionality fullscreen visualblocks preview
    'template paste textcolor colorpicker textpattern imagetools codesample toc' //example_dependency emoticons
],
  toolbar1: 'styleselect | forecolor backcolor bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link media image codesample',
  //theme_advanced_buttons3_add : "visualchars",
  //toolbar2: 'print preview  |   emoticons | table',
  //image_advtab: true,
  templates: <?php echo json_encode((isset($templates)?$templates:[])); ?>,
  document_base_url : "//localhost/gila/",
  content_css: <?php echo json_encode(isset($content_css)?$content_css:[]); ?>
 });
*/
if(typeof requiredRes=='undefined') requiredRes = {}

g.require('lib/jquery/jquery-2.2.4.min.js',function(){
    g.loadCSS('lib/select2/select2.min.css');
    g.require(['lib/select2/select2.min.js'],function(){
        $('.select2').select2();
    })
})

g.dialog.buttons.select_path = {
    title:'Select',fn:function(){
        let v = g('#selected-path').attr('value')
        if(v!=null) g('[name=p_img]').attr('value',v)
        g('#gila-darkscreen').remove();
    }
}
g.dialog.buttons.select_path_post = {
    title:'Select',fn:function(){
        if(!quill.getSelection()) return
        index=quill.getSelection().index
        let v = g('#selected-path').attr('value')
        if(v!=null) quill.insertEmbed(index, 'image', v);
        g('#gila-darkscreen').remove();
    }
}
g.click(".gal-image",function(){
    g('.gal-path').removeClass('g-selected');
    g(this).addClass('g-selected');
    g('#selected-path').attr('value',this.getAttribute('data-path'))
})
g.click(".gal-folder",function(){
    let path=this.getAttribute('data-path')
    g.ajax({url:"admin/media",method:"POST",header:"application/x-www-form-urlencoded",data:"path="+path,fn:function(gal){ //
        g('#gila-popup>.body').html(gal)
    }})
})
g.click("#fm-goup",function(){
    if(this.getAttribute('data-path')=='') return;

    let path=this.getAttribute('data-path')
    g.post("admin/media","path="+path,function(gal){ //
        g('#gila-popup>.body').html(gal)
    })
})

function gallery_upload_files() {
    let fm=new FormData() //g.el('upload_files_form')
    fm.append('uploadfiles', g.el('upload_files').files[0]);
    fm.append('path', g.el('upload_files').getAttribute('data-path'));
    g.ajax({url:"admin/media_upload",method:'POST',data:fm, fn: function (gal){
        g('#gila-popup>.body').html(gal)
    }})
    /*g.upload("admin/media_upload",fm,function(gal){
        g('#gila-popup>.body').html(gal)
    })*/
}

function open_gallery() {
    g.post("admin/media","path=assets",function(gal){ //
        g.dialog({title:"Gila gallery",body:gal,buttons:'select_path'})
    })
}
function open_gallery_post() {
    g.post("admin/media","path=assets",function(gal){ //
        g.dialog({title:"Gila gallery",body:gal,buttons:'select_path_post'})
    })
}
</script>
