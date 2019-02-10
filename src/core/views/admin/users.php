<?php

$links = [];
if(gila::hasPrivilege('admin admin_user')) {
  $links[] = ['Users', function() {
    $type = 'user';
    $src = explode('.',gila::$content[$type])[0];
    view::set('table', $type);
    view::set('tablesrc', $src);
    view::renderFile('admin/content-vue.php');
  }];
}

if(gila::hasPrivilege('admin admin_userrole')) {
  $links[] = ['Roles', function() {
    $type = 'userrole';
    $src = explode('.',gila::$content[$type])[0];
    view::set('table', $type);
    view::set('tablesrc', $src);
    view::renderFile('admin/content-vue.php');
  }];
}

if(gila::hasPrivilege('admin admin_permissions')) {
  $links[] = ['Permissions',function(){
    view::renderFile('admin/permissions.php');
  }];
}

$fn = function(){
  view::renderFile('404.php');
};

?>
<div class="row">
  <ul class="g-nav g-tabs gs-12" id="theme-tabs"><?php
  $tab = router::get('tab',1);
  foreach($links as $key=>$link) {
    if($tab==$key) {
      $active = 'active';
      $fn = $link[1];
    } else $active = '';
    echo '<li class="'.$active.'"><a href="admin/users?tab='.$key.'">'.__($link[0]).'</a></li>';
  }
  ?>
  </ul>
  <div class="tab-content gs-12">
    <div class=''><?php $fn(); ?></div>
  </div>
</div>
