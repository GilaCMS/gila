<?php
$path = router::request('path','assets');
$files = scandir($path);
$disabled = ($path=='assets')?'disabled':'';
$path_array = explode('/',$path);
array_splice($path_array,count($path_array)-1);
$uppath=implode('/',$path_array);

?>

<div id='admin-media-div'><div class='fullwidth bordered inline-flex'>
  <a class='btn btn-white g-group-item' id='fm-goup' data-path='<?=$uppath?>' <?=$disabled?>>
    <i class='fa fa-arrow-left'></i></a>
  <span class='g-group-item' style="padding:var(--main-padding)"><?=$path?></span>
  <input type='file' class='g-group-item g-input fullwidth' id='upload_files' onchange='gallery_upload_files()' multiple data-path="<?=$path?>">
  <span class="g-group-item" style="position:relative;">
      <input class='g-input input-filter' style="height:100%" oninput="filter_files('.gal-path',this.value)" placeholder="filter"/>
      <i class="fa fa-filter" style="position:absolute;margin:12px;right:0;top:0"></i>
  </span>
</div>
<input id='selected-path' type='hidden'>
<div class='g-gal wrapper gap-8px' style='max-height:250px;overflow-y:scroll;background:white'>
<?php

foreach($files as $file) if($file[0]!='.'){
$exp = explode('.',$file);
if (count($exp)==1) {
  $type='folder';
} else {
  $imgx = ['jpg','jpeg','png','gif'];
  if(in_array($exp[count($exp)-1],$imgx)) $type='image'; else $type='file';
}
$filepath=$path.'/'.$file;
if ($type=='image') {
    $img='<img src="'.$filepath.'">';
} else $img='<i class="fa fa-4x fa-'.$type.' " ></i>';
echo '<div data-path="'.$filepath.'"class="gal-path gal-'.$type.'">'.$img.'<br>'.$file.'</div>';
}
echo "</div></div><!--admin-media-div-->";
