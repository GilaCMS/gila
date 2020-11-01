<?php View::includeFile('user-login-header.php');?>

  <div class="gl-4 centered">
    <div class="g-form wrapper g-card bg-white">
      <div class="border-buttom-main_ text-align-center">
        <div style="width:16%;display:inline-block">
          <i class="fa fa-5x fa-check" style="color:green"></i>
        </div>
        <h3><?=__('register_success')?></h3>
      </div>
      <?php if (Config::get('user_activation')=='auto') { ?>
        <a class="btn btn-success btn-block" href="<?=Config::url('login')?>"><?=__('Log In')?></a>
      <?php } elseif (Config::get('user_activation')=='byemail') { ?>
        <p><?=__('register_activate_byemail')?></p>
      <?php } else { ?>
        <p><?=__('register_activate_byadmin')?></p>
      <?php } ?>
    </div>
  </div>

</body>

</html>
