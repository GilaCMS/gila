<?php View::includeFile('user-login-header.php');?>

  <div class="vh-100 h-100 d-flex justify-content-center align-items-center">
    <div class="col-5 border border-2 p-5 rounded">
      <div class="text-center">
        <div>
          <img src="<?=View::thumb(Config::get('admin_logo'))??'assets/gila-logo.png'?>" style="max-height:4em">
        </div>
        <h3><?=__('reset_pass')?></h3>
      </div>
      <form method="post" action="" class="g-form">
        <?=Form::hiddenInput('new_pass')?>
        <p><?=__('reset_submit_pass')?><p>
        <div class="form-group">
          <input class="form-control fullwidth" placeholder="<?=__('New Password')?>" name="pass" type="password" autofocus>
        </div>
        <button type="submit" class="btn btn-primary w-100 mt-1"><?=__('Change Password')?></button>
      </form>
    </div>
  </div>

</body>

</html>
