<?php Gila\View::includeFile('login-header.php');?>

  <?=Gila\View::alerts()?>
  <div class="gl-4 centered wrapper g-card bg-white">
    <div class="border-buttom-main_ text-align-center">
      <div>
        <img src="<?=Gila::config('admin_logo')?:'assets/gila-logo.png'?>" style="max-height:4em">
      </div>
      <h3><?=__('reset_pass')?></h3>
    </div>

    <form role="form" method="post" action="<?=Gila::base_url()?>login/password_reset" class="g-form">
      <p><?=__('reset_pass_msg')?><p>
      <div class="form-group">
        <input class="form-control fullwidth" name="email" type="email" autofocus required>
      </div>
      <input type="submit" class="btn btn-primary btn-block" value="<?=__('Send Email')?>">
    </form>
    <p>
      <a href="login"><?=__('Log In')?></a>
    </p>
  </div>

</body>

</html>
