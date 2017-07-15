<?php
$path = router::post('path','assets');
echo $path;
$files = scandir($path);
$disabled = ($path=='assets')?'disabled':'';
$path_array = explode('/',$path);
array_splice($path_array,count($path_array)-1);
$uppath=implode('/',$path_array);
echo "<div class='g-group fullwidth bordered'><a class='btn btn-white g-group-item' id='fm-goup' data-path='$uppath' $disabled><i class='fa fa-arrow-left'></i></a><span class='g-group-item'>$path</span><input type='file' class='g-group-item' id='upload_files' onchange='gallery_upload_files()' multiple data-path=\"$path\"></div>";
echo "<input id='selected-path' type='hidden'>";
echo "<div class='g-gal wrapper gap-8px' style='max-height:250px;overflow-y:scroll;'>";
foreach($files as $file) if($file[0]!='.'){
$exp = explode('.',$file);
if(count($exp)==1) {
  $type='folder';
} else {
  $imgx = ['jpg','jpeg','png','gif'];
  if(in_array($exp[count($exp)-1],$imgx)) $type='image'; else $type='file';
}
$file=$path.'/'.$file;
if($type=='image') {
    $img='<img src="'.$file.'">';
} else $img='<i class="fa fa-4x fa-'.$type.' " ></i>';
echo '<div data-path="'.$file.'"class="gal-path gal-'.$type.'">'.$img.'<br><span>'.$file.'</span></div>';
}
echo "</div>";

 ?>
<script>
g.click(".gal-image",function(){
    g('.gal-path').removeClass('g-selected');
    g(this).addClass('g-selected');
    g('#selected-path').attr('value',this.getAttribute('data-path'))
})
g.click(".gal-folder",function(){
    let path=this.getAttribute('data-path')
    g.ajax({url:"admin/media",method:"POST",header:"application/x-www-form-urlencoded",data:"g_response=content&path="+path,fn:function(gal){ //
        g('#main-wrapper').html(gal)
    }})
})
g.click("#fm-goup",function(){
    if(this.getAttribute('data-path')=='') return;

    let path=this.getAttribute('data-path')
    g.post("admin/media","g_response=content&path="+path,function(gal){ //
        g('#main-wrapper').html(gal)
    })
})
function gallery_upload_files() {
    let fm=new FormData() //g.el('upload_files_form')
    fm.append('uploadfiles', g.el('upload_files').files[0]);
    fm.append('path', g.el('upload_files').getAttribute('data-path'));
    fm.append('g_response', 'content');
    g.ajax({url:"admin/media_upload",method:'POST',data:fm, fn: function (gal){
        g('#main-wrapper').html(gal)
    }})
}
</script>
