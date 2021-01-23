<?php View::includeFile('user-login-header.php');?>

  <div class="gl-4 centered wrapper g-card bg-white">
    <div class="border-buttom-main_ text-align-center">
      <div>
        <img src="<?=View::thumb(Config::get('admin_logo'))??'assets/gila-logo.png'?>" style="max-height:4em">
      </div>
      <h3><?=__('reset_pass')?></h3>
    </div>
    <form role="form" method="post" action="" class="g-form">
      <?=Form::hiddenInput('new_pass')?>
      <p><?=__('reset_submit_pass')?><p>
      <div class="form-group">
        <input class="form-control fullwidth" placeholder="<?=__('New Password')?>" name="pass" type="password" autofocus>
      </div>
      <button type="submit" class="btn btn-primary btn-block"><?=__('Change Password')?></button>
    </form>
  </div>

</body>

</html>
