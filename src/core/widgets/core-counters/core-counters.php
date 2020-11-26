<?php
  global $db;
  $db->connect();
  $postsC = $db->value('SELECT count(*) from post;');
  $pagesC = $db->value('SELECT count(*) from page;');
  $usersC = $db->value('SELECT count(*) from user;');
  $db->close();
  $packagesC = count($GLOBALS['config']['packages']);
  $palette = Config::get('admin_palette')? json_decode(Config::get('admin_palette'),true): [
    'forestgreen','cornflowerblue','coral','orchid'
  ];
?>
<div class='core-counters-grid'>
<?=View::css('core/widgets.css')?>
<?php if (Session::hasPrivilege('admin')) { ?>
  <div>
    <a href="admin/content/post">
      <div>
        <i class="fa fa-3x fa-pencil" style="color:<?=$palette[0]?>"></i>
        <span>Posts</span>
        <div style="font-size:200%"><?=$postsC?></div>
      </div>
    </a>
  </div>
  <div>
    <a href="admin/users">
      <div>
        <i class="fa fa-3x fa-users" style="color:<?=$palette[1]?>"></i>
        <span>Users</span>
        <div style="font-size:200%"><?=$usersC?></div>
      </div>
    </a>
  </div>
  <div>
    <a href="admin/content/page">
      <div>
        <i class="fa fa-3x fa-file" style="color:<?=$palette[2]?>"></i>
        <span>Pages</span>
        <div style="font-size:200%"><?=$pagesC?></div>
      </div>
    </a>
  </div>
  <div>
    <a href="admin/packages">
      <div>
        <i class="fa fa-3x fa-dropbox" style="color:<?=$palette[3]?>"></i>
        <span>Packages</span>
        <div style="font-size:200%"><?=$packagesC?></div>
      </div>
    </a>
  </div>
<?php } elseif (Session::hasPrivilege('editor')) { ?>
  <div class='gm-6 wrapper'>
    <a href="admin/content/post">
      <div>
        <span>Posts</span>
        <div style="font-size:200%" style="color:<?=$palette[0]?>"><?=$postsC?></div>
      </div>
    </a>
  </div>
  <div class='gm-6 wrapper'>
    <a href="admin/content/page">
      <div>
        <span>Pages</span>
        <div style="font-size:200%" style="color:<?=$palette[2]?>"><?=$pagesC?></div>
      </div>
    </a>
  </div>
<?php } ?>
</div>
