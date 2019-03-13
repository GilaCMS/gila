<?php
$path = router::request('path','assets');
if($path[0]=='.') $path='assets';
$files = scandir($path);
$disabled = ($path=='assets')?'disabled':'';
$path_array = explode('/',$path);
array_splice($path_array,count($path_array)-1);
$uppath=implode('/',$path_array);
view::script('src/core/assets/admin/media.js');
event::fire('admin::media-view', [$path]);
?>

<div id='admin-media-div'><div class='fullwidth bordered inline-flex'>
  <a class='btn btn-white g-group-item' id='fm-goup' data-path='<?=$uppath?>' <?=$disabled?>>
  <i class='fa fa-arrow-left'></i></a>
  <span class='g-group-item' style="padding:var(--main-padding)"><?=$path?></span>
<?php if(gila::hasPrivilege('admin upload_assets')){ ?>
  <input type='file' class='g-group-item g-input fullwidth' id='upload_files' accept="image/*,video/*,audio/*" onchange='gallery_upload_files()' multiple data-path="<?=$path?>">
<?php } ?>
  <span class="g-group-item fullwidth" style="position:relative;">
    <input class='g-input input-filter fullwidth' style="height:100%" oninput="filter_files('.gal-path',this.value)" placeholder="filter"/>
    <i class="fa fa-filter" style="position:absolute;margin:12px;right:0;top:0"></i>
  </span>
  <?php if(gila::hasPrivilege('admin edit_assets')){ ?>
  <button class="btn btn-white" onclick="gallery_create('<?=$path?>')"><i class="fa fa-folder-o"></i></button>
  <button class="btn btn-white" onclick="gallery_move_selected('<?=$path?>')"><strong>N</strong></button>
  <button class="btn btn-white" onclick="gallery_refresh_thumb('<?=$path?>')"><i class="fa fa-refresh"></i></button>
  <button class="btn btn-white" onclick="gallery_delete_selected('<?=$path?>')"><i class="fa fa-trash"></i></button>
  <?php } ?>
</div>
<input id='selected-path' type='hidden'>
<div class='g-gal wrapper gap-8px' style='background:white;overflow-y: scroll;max-height: 400px;'>

<?php
foreach($files as $file) if($file[0]!='.') {
  if (is_dir($path.'/'.$file)) {
    $type='folder';
  } else {
    $type='file';
    $imgx = ['jpg','jpeg','png','gif','svg'];
    if($pinf = pathinfo($file)) if($ext = @$pinf['extension']) {
      if(in_array(strtolower($ext), $imgx)) $type='image';
    }
  }
  $filepath=$path.'/'.$file;
  if ($type=='image') {
    $img='<img src="'.view::thumb($filepath,'media_thumb/',100).'">';
    echo '<div data-path="'.$filepath.'" class="gal-path gal-'.$type.'">'.$img.'<br>'.$file.'</div>';
  }
  if ($type=='folder') {
    $img='<i class="fa fa-5x fa-folder"></i>';
    echo '<div data-path="'.$filepath.'" class="gal-path gal-'.$type.'" >'.$img.'<br>'.$file.'</div>';
  }
  if ($type=='file') {
    $img='<i class="fa fa-4x fa-file-text-o" ></i>';
    echo '<div data-path="'.$filepath.'" class="gal-path gal-'.$type.'" style="opacity:0.4">'.$img.'<br>'.$file.'</div>';
  }
}
echo "</div>";
echo "</div><!--admin-media-div-->";
