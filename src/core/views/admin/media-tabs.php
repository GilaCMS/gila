<style>#media_dialog{border-radius:0;border:0;}
.media-tabs-side{position:absolute;top:0;bottom:0;left:-3em;
width:3em;background:lightsalmon;display: flex;
flex-direction: column;gap: 15px;
align-items: center;padding: 15px 0;
text-align:center}
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
    'title'=>__('My Uploads', ['es'=>'Subidas']),
    'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-upload" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="#444444" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><polyline points="7 9 12 4 17 9" /><line x1="12" y1="4" x2="12" y2="16" /></svg>'
  ],
  [
    'name'=>'assets',
    'title'=>__('Assets', ['es'=>'Recoursos']),
    'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-package" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="#444444" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="12 3 20 7.5 20 16.5 12 21 4 16.5 4 7.5 12 3" /><line x1="12" y1="12" x2="20" y2="7.5" /><line x1="12" y1="12" x2="12" y2="21" /><line x1="12" y1="12" x2="4" y2="7.5" /><line x1="16" y1="5.25" x2="8" y2="9.75" /></svg>'
  ]
], $media_tab_list);

foreach ($media_tab_list as $mtab) {
  $class = ($mtab['name']==$media_tab)? ' style="opacity:1"': '';
  echo '<div data-tab="'.$mtab['name'].'"'.$class.' alt="'.$mtab['title'].'">';
  echo $mtab['icon'].'<div style="font-size:65%">'.$mtab['title'].'</div></div>';
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
