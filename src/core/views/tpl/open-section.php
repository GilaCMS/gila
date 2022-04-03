<?php
$innerPrepend = '';
$backgroundColor = null;
if ($data['_type']=='image-overlay' && empty($data['background']) && !empty($data['image'])) {
  $data['background'] = $data['image'];
}
if ($data['id']=='hero' && isset(Config::$option['theme.hero-image'])) {
  $data['background'] = Config::$option['theme.hero-image'];
  $data['background-color'] = '#000000';
  $data['alfa'] = 0.2;
}
if (!empty($data['background-color'])&&!empty($data['alfa'])&&$data['alfa']>0) {
  $bg = $data['background-color'];
  if ($bg[0]=='#') {
    $backgroundColor = 'rgba('.hexdec($bg[1].$bg[2]).','.hexdec($bg[3].$bg[4]).','.hexdec($bg[5].$bg[6]).','.htmlentities($data['alfa']??'0').')';
  } else {
    $backgroundColor = 'rgba('.$bg.','.htmlentities($data['alfa']??'0').')';
  }
}

$html = '<section';
$html .= $data['id']? ' id="'.$data['id'].'"': '';
$class = 'colors-'.$data['colors'];
$html .= ' class="'.$class.'"';
$style = 'padding:0;position:relative;background-size:cover;';
if (!empty($data['attachment']) && $data['attachment']=='fixed') {
  $style .= 'background-attachment: fixed;';
}
if (!empty($data['padding-top'])) {
  $style .= 'padding-top:'.htmlentities($data['padding-top']).';';
}
if (!empty($data['padding-bottom'])) {
  $style .= 'padding-bottom:'.htmlentities($data['padding-bottom']).';';
}

if (!empty($data['lines-top']) && $backgroundColor) {
  $innerPrepend .= '<svg style="pointer-events:none;top:-4em;position:absolute;left:0;z-index:1" fill="'.$backgroundColor.'" viewBox="0 0 700 90" width="100%" height="4em" preserveAspectRatio="none">'.$pathLines[$data['lines-top']].'</svg>';
}

if (!empty($data['video'])) {
  $innerPrepend .= '<div class="video-overlay-video" style="position: absolute;top: 0;bottom: 0;left: 0;right: 0;overflow:hidden">
    <video src="'.htmlentities($data['video']).'" style="min-height: 100%;min-width:100%;vertical-align: middle;margin-left:50%;transform: translateX(-50%);" autoplay loop muted></video>
  </div>';
} elseif (!empty($data['background'])) {
  $style .= 'background-image:url(\''.htmlentities($data['background']).'\');';
  $style .= 'text-align:'.htmlentities($data['align']??'center').';';
  $style .= 'min-height:'.htmlentities($data['height']).';';
  if (isset($data['positionY'])) {
    $style .= 'background-position:center '.htmlentities($data['positionY']).';';
  } else {
    $style .= 'background-position:'.htmlentities($data['background-position']??'center center').';';
  }
}

if ($backgroundColor) {
  $innerPrepend .= '<div style="background-color:'.$backgroundColor;
  $innerPrepend .= ';position:absolute;width:100%;height:100%;top:0"></div>';
}

$html .= ' style="'.$style.'"';
$html .= '>'.$innerPrepend;
echo $html;
