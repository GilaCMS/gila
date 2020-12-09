<style>#media_dialog{border-radius:0;border:0;}
.media-tabs-side{position:absolute;top:0;bottom:0;left:-3em;
width:3em;background:lightsalmon;display: flex;
flex-direction: column;gap: 15px;
align-items: center;padding: 15px 0;}
.media-tabs-side>div{opacity:0.3;cursor:pointer;}
.media-tabs-side>div:hover{opacity:0.4;}
</style>
<div class="media-tabs-side">
<?php
$default_tab =  Session::key('media_tab') ?? (Config::get('default_media_tab') ?? 'uploads');
$media_tab = Router::request('media_tab', $default_tab);
Session::key('media_tab', $media_tab);

$media_tab_list = Config::getList('media-tab')??[];
$media_tab_list = array_merge([
  [
    'name'=>'uploads',
    'title'=>'My Uploads',
    'icon'=>'<i class="fa fa-2x fa-upload"></i>'
  ],
  [
    'name'=>'assets',
    'title'=>'Assets',
    'icon'=>'<i class="fa fa-2x fa-dropbox"></i>'
  ]
], $media_tab_list);

foreach ($media_tab_list as $mtab) {
  $class = ($mtab['name']==$media_tab)? ' style="opacity:1"': '';
  echo '<div data-tab="'.$mtab['name'].'"'.$class.' alt="'.$mtab['title'].'">';
  echo $mtab['icon'].'</div>';
}
?>
</div>
<?php

if ($media_tab=='uploads') {
  View::renderFile('admin/media-uploads.php');
} elseif ($media_tab=='assets') {
  View::renderFile('admin/media-assets.php');
} else {
  foreach ($media_tab_list as $mt) {
    if ($mt['name']==$media_tab) {
      $view = $mt['view'];
      View::renderFile($view[0], $view[1]);
    }
  }
}
