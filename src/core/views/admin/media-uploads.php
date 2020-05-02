<?php
$upload_folder = Gila::config('media_uploads') ?? 'assets';
$path = Router::request('path', $_COOKIE['media_path']??$upload_folder);
if($path[0]=='.') {
  $path = $upload_folder;
  $monthDir = SITE_PATH.$path.'/'.date("Y-m", time());
  if(!file_exists($monthDir)) {
    mkdir($monthDir);
    $path = $monthDir;
  }
}
setcookie('media_path', $path, time()+86400);
setcookie('media_tab', 'uploads', time()+86400);

$dpath = realpath(SITE_PATH.$upload_folder);
$base = substr(realpath(SITE_PATH.$path), 0, strlen($dpath));
if($base != $dpath) $path = $upload_folder;

$files = scandir(SITE_PATH.$path);
$disabled = ($path==$upload_folder)?'disabled':'';

$path_array = explode('/',$path);
array_splice($path_array,count($path_array)-1);
$uppath=implode('/',$path_array);
$path = rtrim($path, '/');
View::script('src/core/assets/admin/media.js');
View::script('src/core/lang/content/'.Gila::config('language').'.js');
?>

<div id='admin-media-div'><div class='fullwidth bordered inline-flex'>
  <a class='btn btn-white g-group-item' id='fm-goup' data-path='<?=$uppath?>' <?=$disabled?>>
  <i class='fa fa-arrow-left'></i></a>
  <span class='g-group-item' style="padding:var(--main-padding)"><?=$path?></span>
<?php if(Gila::hasPrivilege('admin upload_assets')){ ?>
  <input type='file' class='g-group-item g-input fullwidth' id='upload_files'
  accept="image/*,video/*,audio/*" onchange='gallery_upload_files()'
  multiple data-path="<?=$path?>" data-csrf="<?=gForm::getToken()?>">
<?php } ?>
  <span class="g-group-item fullwidth" style="position:relative;">
    <input class='g-input input-filter fullwidth' style="height:100%" oninput="filter_files('.gal-path',this.value)" placeholder="filter"/>
    <i class="fa fa-filter" style="position:absolute;margin:12px;right:0;top:0"></i>
  </span>
  <?php if(Gila::hasPrivilege('admin edit_assets')){ ?>
  <button class="btn btn-white" onclick="gallery_create('<?=$path?>')"><i class="fa fa-folder-o"></i></button>
  <button class="btn btn-white" onclick="gallery_move_selected('<?=$path?>')"><strong>N</strong></button>
  <button class="btn btn-white" onclick="gallery_refresh_thumb('<?=$path?>')"><i class="fa fa-refresh"></i></button>
  <button class="btn btn-white" onclick="gallery_delete_selected('<?=$path?>')"><i class="fa fa-trash"></i></button>
  <?php } ?>
</div>

<div class='g-gal wrapper gap-8px' style='background:white;'>

<?php
foreach($files as $file) if($file[0]!='.') {
  if (is_dir(SITE_PATH.$path.'/'.$file)) {
    $type='folder';
  } else {
    $type='file';
    $imgx = ['jpg','jpeg','png','gif','svg'];
    if($pinf = pathinfo(SITE_PATH.$file)) if($ext = @$pinf['extension']) {
      if(in_array(strtolower($ext), $imgx)) $type='image';
    }
    $vidx = ['avi','webm','mp4','mkv'];
    if($pinf = pathinfo(SITE_PATH.$file)) if($ext = @$pinf['extension']) {
      if(in_array(strtolower($ext), $vidx)) $type='video';
    }
  }
  $filepath=$path.'/'.$file;
  $filename=htmlentities($file);
  if ($type=='image') {
    $img='<img src="'.View::thumb(SITE_PATH.$filepath,'media_thumb/',100).'">';
    echo '<div data-path="'.SITE_PATH.$filepath.'" class="gal-path gal-'.$type.'">'.$img.'<br>'.$filename.'</div>';
  }
  if ($type=='video') {
    $img='<i class="fa fa-5x fa-film"></i>';
    echo '<div data-path="'.SITE_PATH.$filepath.'" class="gal-path gal-image">'.$img.'<br>'.$filename.'</div>';
  }
  if ($type=='folder') {
    $img='<i class="fa fa-5x fa-folder-o"></i>';
    echo '<div data-path="'.$filepath.'" class="gal-path gal-'.$type.'" >'.$img.'<br>'.$filename.'</div>';
  }
  if ($type=='file') {
    $img='<i class="fa fa-4x fa-file-text-o" ></i>';
    echo '<div data-path="'.SITE_PATH.$filepath.'" class="gal-path gal-'.$type.'" style="opacity:0.4">'.$img.'<br>'.$filename.'</div>';
  }
}
echo "</div>";
echo "</div><!--admin-media-div-->";
