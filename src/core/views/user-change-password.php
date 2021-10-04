<?php View::includeFile('user-login-header.php');?>

  <?=View::alerts()?>
  <div class="gl-4 centered wrapper g-card bg-white">
    <div class="border-buttom-main_ text-align-center">
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
      <button type="submit" class="btn btn-primary btn-block"><?=__('Send Email')?></button>
    </form>
    <p>
      <a href="<?=Config::base('user')?>"><?=__('Log In')?></a>
    </p>
  </div>

</body>

</html>
