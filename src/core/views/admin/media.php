<?php
event::fire('admin::media-view', [$path]);

echo "<input id='selected-path' type='hidden'>";

if($_REQUEST['g_response']=='content') {
  include 'media-tabs.php';
} else {
  view::renderFile('admin/media-uploads.php');
}
