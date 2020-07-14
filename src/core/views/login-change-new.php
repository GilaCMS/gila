<?php View::includeFile('login-header.php');?>

  <div class="gl-4 centered wrapper g-card bg-white">
    <div class="border-buttom-main_ text-align-center">
      <div>
        <img src="<?=Gila::config('admin_logo')?:'assets/gila-logo.png'?>" style="max-height:4em">
      </div>
      <h3><?=__('reset_pass')?></h3>
    </div>
    <form role="form" method="post" action="" class="g-form">
      <p><?=__('reset_submit_pass')?><p>
      <div class="form-group">
        <input class="form-control fullwidth" placeholder="<?=__('New Password')?>" name="pass" type="password" autofocus>
      </div>
      <div class="form-group">
        <input class="form-control fullwidth" placeholder="<?=__('Confirm Password')?>" name="pass2" type="password" autofocus>
      </div>
      <input type="submit" class="btn btn-primary btn-block" value="<?=__('Change Password')?>">
    </form>
  </div>

</body>

</html>
