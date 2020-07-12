<?php

echo "<input id='selected-path' type='hidden'>";
echo "<input id='selected-image-caption' type='hidden'>";

if ($_REQUEST['g_response']=='content') {
  include 'media-tabs.php';
} else {
  Gila\View::renderFile('admin/media-uploads.php');
}
