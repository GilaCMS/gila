<?php
$upload_folder = Config::get('media_uploads') ?? 'assets';
$path = Router::request('path', Session::key('media_path') ?? $upload_folder);
if ($path[0]=='.') {
  $path = $upload_folder;
  $monthDir = SITE_PATH.$path.'/'.date("Y-m", time());
  if (!file_exists($monthDir)) {
    mkdir($monthDir);
    $path = $monthDir;
  }
}
Session::key('media_path', $path);
Session::key('media_tab', 'uploads');

$dpath = realpath(SITE_PATH.$upload_folder);
$base = substr(realpath(SITE_PATH.$path), 0, strlen($dpath));
if ($base != $dpath) {
  $path = $upload_folder;
}

$files = scandir(SITE_PATH.$path);
$disabled = ($path==$upload_folder)?'disabled':'';

$path_array = explode('/', $path);
array_splice($path_array, count($path_array)-1);
$uppath=implode('/', $path_array);
$path = rtrim($path, '/');
View::script('core/admin/media.js');
View::script('core/lang/content/'.Config::get('language').'.js');
?>

<div id='admin-media-div'><div class='fullwidth' style="gap:0.2em;display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));align-items:center">
  <span class='g-group-item' style="padding:var(--main-padding)">
    <a class='btn btn-white g-group-item' id='fm-goup' data-path='<?=$uppath?>' <?=$disabled?>>
    &larr; <?=$path?></a>
  </span>
<?php if (Session::hasPrivilege('admin upload_assets')) { ?>
  <input type='file' class='g-group-item g-input fullwidth' id='upload_files'
  accept="image/*,video/*,audio/*" onchange='gallery_upload_files()'
  multiple data-path="<?=$path?>" data-csrf="<?=Form::getToken()?>">
<?php } ?>
  <span class="g-group-item fullwidth" style="position:relative;padding:0">
    <input class='g-input input-filter fullwidth' style="margin:0" oninput="filter_files('.gal-path',this.value)" placeholder="filter"/>
    <img src="assets/core/admin/filter.svg" class="img-btn" style="max-height:18px;position:absolute;margin:0.3em;right:0.3em;top:0.3em"></i>
  </span>
  <?php if (Session::hasPrivilege('admin edit_assets')) { ?>
  <span style="display:flex;height:min-content">
  <button class="btn btn-white" title="<?=__('Add Folder')?>" onclick="gallery_create('<?=$path?>')"><img style="min-height:16px;" src="assets/core/admin/folder-plus.svg"></button>
  <button class="btn btn-white" title="<?=__('Rename')?>" onclick="gallery_move_selected('<?=$path?>')"><img style="min-height:16px;" src="assets/core/admin/pencil.svg"></button>
  <button class="btn btn-white" title="<?=__('Refresh Thumbnail')?>" onclick="gallery_refresh_thumb('<?=$path?>')"><img style="min-height:16px;" src="assets/core/admin/refresh.svg"></button>
  <button class="btn btn-white" title="<?=__('Delete')?>" onclick="gallery_delete_selected('<?=$path?>')"><img style="mi-height:16px;" src="assets/core/admin/trash.svg"></button>
  </span>
  <?php } ?>
</div>

<div class='g-gal wrapper gap-8px' style='background:white;'
ondrop='gallery_drop_files(event);' ondragover='event.preventDefault();'>

<?php
foreach ($files as $file) {
  if ($file[0]!='.') {
    if (is_dir(SITE_PATH.$path.'/'.$file)) {
      $filepath=$path.'/'.$file;
      $filename=htmlspecialchars($file);
      $type='folder';
      $img = '<img src="assets/core/admin/folder.svg">';
      echo '<div data-path="'.$filepath.'" class="gal-path gal-'.$type.'" >'.$img.'<br>'.$filename.'</div>';
    }
  }
}

foreach ($files as $file) {
  if ($file[0]!='.') {
    if (!is_dir(SITE_PATH.$path.'/'.$file)) {
      $type='file';
      $imgx = ['jpg','jpeg','png','gif','svg','webp'];
      if ($pinf = pathinfo(SITE_PATH.$file)) {
        if ($ext = @$pinf['extension']) {
          if (in_array(strtolower($ext), $imgx)) {
            $type='image';
          }
        }
      }
      $vidx = ['avi','webm','mp4','mkv','mp3'];
      if ($pinf = pathinfo(SITE_PATH.$file)) {
        if ($ext = @$pinf['extension']) {
          if (in_array(strtolower($ext), $vidx)) {
            $type='video';
          }
        }
      }
      $filepath=$path.'/'.$file;
      $filename=htmlspecialchars($file);
      if ($type=='image') {
        $img='<img src="'.View::thumb(SITE_PATH.$filepath, 100).'">';
        echo '<div data-path="'.SITE_PATH.$filepath.'" class="gal-path gal-'.$type.'">'.$img.'<br>'.$filename.'</div>';
      }
      if ($type=='video') {
        $img = '<img src="assets/core/admin/movie.svg">';
        echo '<div data-path="'.SITE_PATH.$filepath.'" class="gal-path gal-image">'.$img.'<br>'.$filename.'</div>';
      }
      if ($type=='file') {
        $img = '<img src="assets/core/admin/file.svg">';
        echo '<div data-path="'.SITE_PATH.$filepath.'" class="gal-path gal-'.$type.'" style="opacity:0.4">'.$img.'<br>'.$filename.'</div>';
      }
    }
  }
}
echo "</div>";
if ($total = Config::get('media_uploads_limit')) {
  $size = Cache::remember('fsize', 8000, function () {
    return FileManager::getUploadsSize();
  });
  $mb = round($size/(1024*1024), 1);
  echo '<progress value="'.$mb.'" max="'.$total.'"> '.round(100*$size/$total).'% </progress>';
  echo ' <span>'.round(100*$size/$total).'% from '.$total.' MB used</span>';
}
echo "</div><!--admin-media-div-->";
