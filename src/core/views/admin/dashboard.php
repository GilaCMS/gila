
<?php view::alerts()?>
<div class='row'>
  <div class='gm-3 wrapper'>
    <a href="admin/content/post">
        <div class='alert' style="background:olivedrab">
            <span>Posts</span>
            <div style="font-size:200%"><?=$c->posts?></div>
        </div>
    </a>
  </div>
  <div class='gm-3 wrapper'>
    <a href="admin/content/user">
        <div class='alert' style="background:cornflowerblue">
            <span>Users</span>
            <div style="font-size:200%"><?=$c->users?></div>
        </div>
    </a>
  </div>
  <div class='gm-3 wrapper'>
    <a href="admin/content/page">
        <div class='alert' style="background:coral">
            <span>Pages</span>
            <div style="font-size:200%"><?=$c->pages?></div>
        </div>
    </a>
  </div>
  <div class='gm-3 wrapper'>
    <a href="admin/packages">
        <div class='alert' style="background:orchid">
            <span>Packages</span>
            <div style="font-size:200%"><?=$c->packages?></div>
        </div>
    </a>
  </div>
</div>

<div class="widget-area-dashboard wrapper">
    <?php view::widget_area('dashboard'); ?>
</div>
