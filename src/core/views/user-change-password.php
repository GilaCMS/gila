<?php View::includeFile('user-login-header.php');?>

  <?=View::alerts()?>
  <div class="vh-100 h-100 d-flex justify-content-center align-items-center">
    <div class="col-5 border border-2 p-5 rounded">
      <div class="text-center">
        <div>
          <img src="<?=View::thumb(Config::get('login_logo'))??'assets/gila-logo.png'?>" style="max-height:4em">
        </div>
        <h3><?=__('reset_pass')?></h3>
      </div>

      <form method="post" action="<?=Config::base('user/password_reset')?>" class="g-form">
        <?=Form::hiddenInput('reset_pass')?>
        <p><?=__('reset_pass_msg')?><p>
        <div class="form-group">
          <input class="form-control fullwidth" name="email" type="email" autofocus required>
        </div>
        <button type="submit" class="btn btn-primary w-100 mt-1"><?=__('Send Email')?></button>
      </form>
      <p>
        <a href="<?=Config::base('user')?>" rel="nofollow"><?=__('Log In')?></a>
      </p>
    </div>
  </div>

</body>

</html>
