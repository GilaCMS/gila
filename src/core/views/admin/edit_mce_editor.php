<?php
$pages_path = [];
$templates = [];

$pages_path[] = view::getThemePath().'/pages/';
if(view::$parent_theme) $pages_path[] = 'themes/'.view::$parent_theme.'/pages/';
$pages_path = array_merge($pages_path, gila::packages());
$pages_path[] = 'src/core/templates/';
foreach($pages_path as $path) {
    if(file_exists($path)) {
      $pages = scandir($path);
      foreach ($pages as $page) if($page[0]!='.'){
        $templates[] = [
          'title'=>$page, 'url'=>$path.$page
          //description: 'Some desc 1',
          //file_get_contents($pages_path.$page);
          ];
      }
    }
}
?>

<textarea name="<?=$textarea?>"><?=isset($p->page)?$p->page:$p->post?></textarea>

<?=view::script("lib/tinymce/tinymce.min.js")?>

<script>
base_url = "<?=gila::config('base')?>"
function get_text_to_save() {
    return g('[name=p_post]').all[0].innerHTML;
}


tinymce.init({
  selector: '[name=<?=$textarea?>]',
  relative_urls: false,
  remove_script_host: false,
  //mode: 'none',
  height: 300,
  theme: 'modern',
  plugins: [
    'lists link image charmap hr anchor pagebreak',
    'searchreplace wordcount visualchars code',
    'insertdatetime media nonbreaking table contextmenu ',
    'template paste textcolor colorpicker textpattern codesample'
  ],
  toolbar1: 'styleselect | forecolor backcolor bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link media image codesample',
  templates: <?php echo json_encode((isset($templates)?$templates:[])); ?>,
  document_base_url : "<?=gila::config('base')?>",
  content_css: <?php echo json_encode(view::$stylesheet); //isset($content_css)?$content_css:[] ?>,
  file_picker_callback: function(cb, value, meta) {
    input_filename = cb;
    open_gallery_post();
  },

 });

if(typeof requiredRes=='undefined') requiredRes = {}

g.require('lib/jquery/jquery-3.3.1.min.js',function(){
    g.loadCSS('lib/select2/select2.min.css');
    g.require(['lib/select2/select2.min.js'],function(){
        $('.select2').select2();
    })
})

g.dialog.buttons.select_path = {
    title:'Select',fn: function(){
        let v = g('#selected-path').attr('value')
        if(v!=null) g('[name=p_img]').attr('value', base_url+v)
        g('#media_dialog').parent().remove();
    }
}
g.dialog.buttons.select_path_post = {
    title:'Select', fn: function() {
        let v = g('#selected-path').attr('value')
        if(v!=null) input_filename(base_url+v);
        g('#media_dialog').parent().remove();
    }
}
function open_gallery() {
    g.post("admin/media","g_response=content&path=assets",function(gal){
        g.dialog({title:"Media gallery",body:gal,buttons:'select_path',type:'modal',class:'large',id:'media_dialog'})
    })
}
function open_gallery_post() {
    g.post("admin/media","g_response=content&path=assets",function(gal){ //
        g.dialog({title:"Media gallery",body:gal,buttons:'select_path_post',type:'modal',class:'large',id:'media_dialog'})
    })
}


</script>
