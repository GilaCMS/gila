<h1>Theme options</h1>
<?php
$pack = gila::config('theme');
echo '<form id="theme_options_form" class="g-form">';
echo '<input id="theme_id" value="'.$pack.'" type="hidden">';

if(file_exists('themes/'.$pack.'/package.json')) {
  $pac=json_decode(file_get_contents('themes/'.$pack.'/package.json'),true);
  $options=$pac['options'];
} else include 'themes/'.$pack.'/package.php';

if(is_array($options)) {
  foreach($options as $key=>$op) {
    $values[$key] = gila::option('theme.'.$key);
  }
  echo gForm::html($options,$values,'option[',']');
}// else error alert
echo "</form>";
?>
<button class="g-btn" onclick="theme_save_options()">Update</button>

<?=view::script('src/core/assets/admin/media.js')?>
<?=view::script('lib/vue/vue.min.js');?>
<?=view::script('src/core/lang/content/'.gila::config('language').'.js');?>
<?=view::script('src/core/assets/admin/listcomponent.js');?>
<script>

app = new Vue({
  el: '#theme_options_form'
})

theme_save_options = function() {
  let p = g.el('theme_id').value;
  let fm=new FormData(g.el('theme_options_form'))
  g.loader()
  g.ajax({url:'admin/themes?g_response=content&save_options='+p,method:'POST',data:fm,fn:function(x){
    g.loader(false)
    g('.gila-darkscreen').remove();
  }})
}


</script>
