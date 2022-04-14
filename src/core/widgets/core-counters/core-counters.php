<?php
  DB::connect();
  $data['postsC'] = DB::value('SELECT count(*) from post;');
  $data['pagesC'] = DB::value('SELECT count(*) from page;');
  $data['usersC'] = DB::value('SELECT count(*) from user;');
  DB::close();
  $data['packagesC'] = count(Config::getArray('packages'));
  $data['palette'] = Config::getArray('admin_palette') ?? ['forestgreen','cornflowerblue','coral','orchid'];
  $data['isAdmin'] = Session::hasPrivilege('admin');
  $data['isEditor'] = Session::hasPrivilege('editor');
  $data['postsText'] = __('Posts', ['es'=>'Publicaciones','es'=>'Δημοσιεύσεις']);
  $data['usersText'] = __('Users', ['es'=>'Usuarios','es'=>'Χρηστες']);
  $data['pagesText'] = __('Pages', ['es'=>'Páginas','es'=>'Σελίδες']);
  $data['packagesText'] = __('Packages', ['es'=>'Paquetes','es'=>'Πακέτα']);

  if (class_exists('Mustache_Engine')) {
    $m = new Mustache_Engine;
    $tmp = empty($data['mustache'])? file_get_contents(__DIR__.'/widget.mustache'): $data['mustache'];
    echo View::css('core/widgets.css');
    echo $m->render($tmp, $data);
    return;
  }
?>
<div class='core-counters-grid'>
<?=View::css('core/widgets.css')?>
<?php if (Session::hasPrivilege('admin')) { ?>
  <div>
    <a href="admin/content/post">
      <div>
        <i class="fa fa-3x fa-pencil" style="color:<?=$data['palette'][0]?>"></i>
        <span><?=__('Posts', ['es'=>'Publicaciones','es'=>'Δημοσιεύσεις'])?></span>
        <div style="font-size:200%"><?=$data['postsC']?></div>
      </div>
    </a>
  </div>
  <div>
    <a href="admin/users">
      <div>
        <i class="fa fa-3x fa-users" style="color:<?=$data['palette'][1]?>"></i>
        <span><?=__('Users', ['es'=>'Usuarios','es'=>'Χρηστες'])?></span>
        <div style="font-size:200%"><?=$data['usersC']?></div>
      </div>
    </a>
  </div>
  <div>
    <a href="admin/content/page">
      <div>
        <i class="fa fa-3x fa-file" style="color:<?=$data['palette'][2]?>"></i>
        <span><?=__('Pages', ['es'=>'Páginas','es'=>'Σελίδες'])?></span>
        <div style="font-size:200%"><?=$data['pagesC']?></div>
      </div>
    </a>
  </div>
  <div>
    <a href="admin/packages">
      <div>
        <i class="fa fa-3x fa-dropbox" style="color:<?=$data['palette'][3]?>"></i>
        <span><?=__('Packages', ['es'=>'Paquetes','es'=>'Πακέτα'])?></span>
        <div style="font-size:200%"><?=$data['packagesC']?></div>
      </div>
    </a>
  </div>
<?php } elseif (Session::hasPrivilege('editor')) { ?>
  <div class='gm-6 wrapper'>
    <a href="admin/content/post">
      <div>
        <span>Posts</span>
        <div style="font-size:200%" style="color:<?=$data['palette'][0]?>"><?=$data['postsC']?></div>
      </div>
    </a>
  </div>
  <div class='gm-6 wrapper'>
    <a href="admin/content/page">
      <div>
        <span>Pages</span>
        <div style="font-size:200%" style="color:<?=$data['palette'][2]?>"><?=$data['pagesC']?></div>
      </div>
    </a>
  </div>
<?php } ?>
</div>
