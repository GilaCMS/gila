<?php View::includeFile('user-login-header.php');?>

  <div class="vh-100 h-100 d-flex justify-content-center align-items-center">
    <div class="col-5 border border-2 p-5 rounded">
      <div class="text-center">
        <div>
          <i class="fa fa-5x fa-check" style="color:green"></i>
        </div>
        <h3><?=__('register_success')?></h3>
      </div>
      <?php if (Config::get('user_activation')=='auto') : ?>
        <a class="btn btn-success btn-block" href="<?=Config::url('user')?>"><?=__('Log In')?></a>
      <?php elseif (Config::get('user_activation')=='byemail') : ?>
        <p><?=__('register_activate_byemail')?></p>
      <?php else : ?>
        <p><?=__('register_activate_byadmin')?></p>
      <?php endif; ?>
    </div>
  </div>

</body>

</html>
