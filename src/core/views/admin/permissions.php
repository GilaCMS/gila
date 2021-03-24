<?php
global $db;

// load all permissions
$permissions = Gila\Profile::getAllPermissions();

// load all user groups
$roles = array_merge([['member','*']], $db->get("SELECT id,userrole FROM userrole;"));

// update permissions if form submited
if (isset($_POST['submit']) && isset($_POST['role'])) {
  $checked = [];
  foreach ($_POST['role'] as $role=>$list) {
    if (is_array($list)) {
      $checked[$role] = array_keys($list);
    }
  }
  Gila\Config::set('permissions', $checked);
  Gila\View::alert('success', __('_changes_updated'));
}

$checked = Gila\Config::getArray('permissions');


View::alerts();
?>
<br>
<form action="<?=Gila\Config::url('admin/users', ['tab'=>2])?>" method="POST">
<button class="g-btn" name="submit" value="true">
  <i class="fa fa-save"></i> <?=__('Submit')?>
</button>
<br>

<style>table th:nth-child(2),table th:nth-child(2){font-weight: lighter;}</style>
<table id="tbl-permissions" class="g-table">
  <tr>
    <th><?php foreach ($roles as $role) {
  echo '<th style="text-align:center">'.$role[1];
} ?>
  </tr>
  <?php foreach ($permissions as $index=>$permission) { ?>
  <tr>
    <td>
      <?=__($index, $permission)?>
    <?php foreach ($roles as $role) {
  echo '<td style="text-align:center">';
  if ($index==='admin' && $role[0]==='member') {
    echo '';
  } else {
    echo '<input type="checkbox" name="role['.$role[0].']['.$index.']"';
    if (isset($checked[$role[0]]) && in_array($index, $checked[$role[0]])) {
      echo ' checked';
    }
    echo '>';
  }
} ?>
  </tr>
  <?php } ?>
</table>
</form>
