
<?php View::alerts()?>
<style>#main-wrapper>div{background: inherit !important;border:none}</style>
<div class='row'>
<?php if (Session::hasPrivilege('admin')) { ?>
  <div class='gm-3 wrapper'>
    <a href="admin/content/post">
      <div class='alert' style="background:olivedrab">
        <span>Posts</span>
        <div style="font-size:200%"><?=$postsC?></div>
      </div>
    </a>
  </div>
  <div class='gm-3 wrapper'>
    <a href="admin/users">
      <div class='alert' style="background:cornflowerblue">
        <span>Users</span>
        <div style="font-size:200%"><?=$usersC?></div>
      </div>
    </a>
  </div>
  <div class='gm-3 wrapper'>
    <a href="admin/content/page">
      <div class='alert' style="background:coral">
        <span>Pages</span>
        <div style="font-size:200%"><?=$pagesC?></div>
      </div>
    </a>
  </div>
  <div class='gm-3 wrapper'>
    <a href="admin/packages">
      <div class='alert' style="background:orchid">
        <span>Packages</span>
        <div style="font-size:200%"><?=$packagesC?></div>
      </div>
    </a>
  </div>
<?php } elseif (Session::hasPrivilege('editor')) { ?>
  <div class='gm-6 wrapper'>
    <a href="admin/content/post">
      <div class='alert' style="background:olivedrab">
        <span>Posts</span>
        <div style="font-size:200%"><?=$postsC?></div>
      </div>
    </a>
  </div>
  <div class='gm-6 wrapper'>
    <a href="admin/content/page">
      <div class='alert' style="background:coral">
        <span>Pages</span>
        <div style="font-size:200%"><?=$pagesC?></div>
      </div>
    </a>
  </div>
<?php } ?>
</div>

<div class="widget-area-dashboard wrapper">
  <?php View::widgetArea('dashboard'); ?>
</div>
