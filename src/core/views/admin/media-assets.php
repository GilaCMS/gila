<?php
$path = Gila\Router::request('path', Gila\Session::key('asset_path') ?? 'src');
if ($path[0]=='.') {
  $path = 'src';
}

$acceptedPath = false;
$scanned = scandir('src/');
if ($path!='src') {
  foreach ($scanned as $i=>$v) {
    if (is_dir('src/'.$v)) {
      $package = json_decode(file_get_contents('src/'.$v.'/package.json'));
      if (isset($package->assets)) {
        foreach ($package->assets as $asset) {
          $dpath = realpath('src/'.$v.'/'.$asset);
          $base = substr(realpath($path), 0, strlen($dpath));
          if ($base == $dpath) {
            $acceptedPath = true;
          }
        }
      }
    }
  }
  if ($acceptedPath == false) {
    $path = 'src';
  }
}
Gila\Session::key('asset_path', $path);
Gila\Session::key('media_tab', 'assets');
$disabled = ($path=='')?'disabled':'';

$files=[];
if ($path=='src') {
  $scanned = scandir('src/');
  foreach ($scanned as $i=>$v) {
    $jsonFile = 'src/'.$v.'/package.json';
    if ($v[0]!='.' && file_exists($jsonFile)) {
      $package = json_decode(file_get_contents($jsonFile));
      if (isset($package->assets)) {
        foreach ($package->assets as $asset) {
          $a = $asset;
          if ($a == 'assets') {
            $a = '';
          }
          if (substr($a, 0, 7) == 'assets/') {
            $a = substr($a, 7);
          }
          if (file_exists('assets/'.$v.'/'.$a)) {
            $files[] = 'src/'.$v.'/'.$asset;
          }
        }
      }
    }
  }
} else {
  $path_array = explode('/', $path);
  array_splice($path_array, count($path_array)-1);
  if (count($path_array)<3) {
    $uppath = 'src';
  } else {
    $uppath=implode('/', $path_array);
  }
  $path = rtrim($path, '/');
  $files = scandir($path);
  foreach ($files as $i=>$v) {
    $files[$i] = $path.'/'.$files[$i];
  } ?>
<a class='btn btn-white g-group-item' id='fm-goup' data-path='<?=$uppath?>' <?=$disabled?>>
&larr; <?=$uppath?></a>
<span class="g-group-item" style="position:relative;">
  <input class='g-input input-filter' oninput="filter_files('.gal-path',this.value)" placeholder="filter"/>
  <img src="assets/core/admin/filter.svg" class="img-btn" style="max-height:18px;position:absolute;margin:0.3em;right:0.3em;top:0">
</span>

  <?php
}

Gila\View::script('core/admin/media.js');
Gila\View::script('core/lang/content/'.Config::get('language').'.js');
?>
<div id='admin-media-div'>
<div class='g-gal wrapper gap-8px' style='background:white;'>

<?php
foreach ($files as $filepath) {
  if (substr($filepath, -1)!='.') {
    if (is_dir($filepath)) {
      $type='folder';
    } else {
      $type='file';
      $imgx = ['jpg','jpeg','png','gif','svg','webp'];
      if ($pinf = pathinfo($filepath)) {
        if ($ext = @$pinf['extension']) {
          if (in_array(strtolower($ext), $imgx)) {
            $type='image';
          }
        }
        $filepath = 'assets/'.substr(strtr($filepath, ['/assets/'=>'/']), 4);
      }
    }

    $basename = substr($filepath, strrpos($filepath, '/', -1)+1);
    if ($path=='src') {
      $folders = explode('/', $filepath);
      if ($basename!='assets') {
        $basename = $folders[1].':'.$basename;
      } else {
        $basename = $folders[1];
      }
    }

    if ($type=='image') {
      $img='<img src="'.Gila\View::thumb($filepath, 'media_thumb/', 100).'">';
      echo '<div data-path="'.$filepath.'" class="gal-path gal-'.$type.'">'.$img.'<br>'.$basename.'</div>';
    }
    if ($type=='folder') {
      $img = '<img src="assets/core/admin/folder.svg">';
      echo '<div data-path="'.$filepath.'" class="gal-path gal-'.$type.'" >'.$img.'<br>'.$basename.'</div>';
    }
  }
}

echo "</div></div><!--admin-media-div-->";
