<?php
if (!empty($data['background-color'])&&!empty($data['alfa'])&&$data['alfa']>0) {
  $bg = $data['background-color'];
  if ($bg[0]=='#') {
    $backgroundColor = 'rgba('.hexdec($bg[1].$bg[2]).','.hexdec($bg[3].$bg[4]).','.hexdec($bg[5].$bg[6]).','.htmlentities($data['alfa']??'0').')';
  } else {
    $backgroundColor = 'rgba('.$bg.','.htmlentities($data['alfa']??'0').')';
  }
  if (!empty($data['lines-bottom'])) {
    echo '<svg style="pointer-events:none;transform:rotate(180deg);width:100%;position:absolute;z-index:1;bottom:-4em;left: 0;" fill="'.$backgroundColor.'" viewBox="0 0 700 90" width="100%" height="4em" preserveAspectRatio="none">'.$pathLines[$data['lines-bottom']].'</svg>';
  }
}
?>
</section>
