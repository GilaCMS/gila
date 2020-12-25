<?php View::includeFile('user-login-header.php');?>

  <?php View::alerts()?>
  <div class="gl-4 centered wrapper g-card bg-white">
    <div class="border-buttom-main_ text-align-center">
      <div>
        <img src="<?=Config::get('admin_logo')??'assets/gila-logo.png'?>" style="max-height:4em">
      </div>
      <h3><?=__('Register')?></h3>
    </div>

    <form role="form" method="post" action="" class="g-form">
      <label><?=__('Name')?></label>
      <div class="form-group">
        <input class="form-control fullwidth" name="name" autofocus required>
      </div>
      <label><?=__('Email')?></label>
      <div class="form-group">
        <input class="form-control fullwidth" name="email" type="email" required>
      </div>
      <label><?=__('Password')?></label>
      <div class="form-group ">
        <input class="form-control fullwidth" name="password" type="password" value="" required>
      </div>
      <?php Gila\Event::fire('recaptcha.form')?>
      <button type="submit" class="btn btn-primary btn-block"><?=__('Register')?></button>
    </form>
    <p>
      <a href="login"><?=__('Log In')?></a>
    </p>
  </div>

</body>

</html>
