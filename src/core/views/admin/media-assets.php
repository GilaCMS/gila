<?php
$path = router::request('path', session::key('asset_path')??'src');
if($path[0]=='.') $path = 'src';
session::key('asset_path', $path);
$disabled = ($path=='')?'disabled':'';

$files=[];
if($path=='src') {
  $scanned = scandir('src/');
  foreach($scanned as $i=>$v) if(is_dir('src/'.$v)) {
    $package = json_decode(file_get_contents('src/'.$v.'/package.json'));
    if(isset($package->assets)) {
      foreach($package->assets as $asset) {
        $files[] = 'src/'.$v.'/'.$asset;
      }
    }
  }
/*  $files = array_merge($files, $scanned);
  foreach(gila::packages() as $p) {
    $scanned = scandir('src/'.$p.'/assets/');
    foreach($scanned as $i=>$v) {    
      $scanned[$i] = 'src/'.$p.'/assets/'.$scanned[$i];
    }
    if($scanned) $files = array_merge($files, $scanned);
  }*/
} else {
  $path_array = explode('/',$path);
  array_splice($path_array,count($path_array)-1);
  if(count($path_array)<3) {
    $uppath = 'src';
  } else {
    $uppath=implode('/',$path_array);
  }
  $path = rtrim($path, '/');
  $files = scandir($path);
  foreach($files as $i=>$v) {
    $files[$i] = $path.'/'.$files[$i];
  } ?>
<a class='btn btn-white g-group-item' id='fm-goup' data-path='<?=$uppath?>' <?=$disabled?>>
<i class='fa fa-arrow-left'></i></a>
<span class='g-group-item' style="padding:var(--main-padding)"><?=$path?></span>

  <?php
}

view::script('src/core/assets/admin/media.js');
?>
<div id='admin-media-div'>
<div class='g-gal wrapper gap-8px' style='background:white;'>

<?php
foreach($files as $filepath) if(substr($filepath, -1)!='.') {
  if (is_dir($filepath)) {
    $type='folder';
  } else {
    $type='file';
    $imgx = ['jpg','jpeg','png','gif','svg'];
    if($pinf = pathinfo($filepath)) if($ext = @$pinf['extension']) {
      if(in_array(strtolower($ext), $imgx)) $type='image';
    }
  }

  $basename = substr($filepath, strrpos($filepath, '/', -1)+1);
  if($path=='src') {
    $folders = explode('/', $filepath);
    $basename = $folders[1].':'.$basename;
  }

  if ($type=='image') {
    $img='<img src="'.view::thumb($filepath,'media_thumb/',100).'">';
    echo '<div data-path="'.$filepath.'" class="gal-path gal-'.$type.'">'.$img.'<br>'.$basename.'</div>';
  }
  if ($type=='folder') {
    $img='<i class="fa fa-5x fa-folder"></i>';
    echo '<div data-path="'.$filepath.'" class="gal-path gal-'.$type.'" >'.$img.'<br>'.$basename.'</div>';
  }
}

echo "</div></div><!--admin-media-div-->";
