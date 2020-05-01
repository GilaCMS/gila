<?php
Event::fire('admin::media-view', [$path]);

echo "<input id='selected-path' type='hidden'>";
echo "<input id='selected-image-caption' type='hidden'>";

if($_REQUEST['g_response']=='content') {
  include 'media-tabs.php';
} else {
  View::renderFile('admin/media-uploads.php');
}
