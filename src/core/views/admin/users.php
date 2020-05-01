<?php

$links = [];
if(Gila::hasPrivilege('admin admin_user')) {
  $links[] = ['Users', function() {
    $type = 'user';
    $src = explode('.',Gila::$content[$type])[0];
    View::set('table', $type);
    View::set('tablesrc', $src);
    View::renderFile('admin/content-vue.php');
  }];
}

if(Gila::hasPrivilege('admin admin_userrole')) {
  $links[] = ['Roles', function() {
    $type = 'userrole';
    $src = explode('.',Gila::$content[$type])[0];
    View::set('table', $type);
    View::set('tablesrc', $src);
    View::renderFile('admin/content-vue.php');
  }];
}

if(Gila::hasPrivilege('admin admin_permissions')) {
  $links[] = ['Permissions',function(){
    View::renderFile('admin/permissions.php');
  }];
}

$fn = function(){
  http_response_code(404);
  View::renderFile('404.php');
};

?>
<div class="row">
  <ul class="g-nav g-tabs gs-12" id="theme-tabs"><?php
  $tab = Router::get('tab',1);
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
