<?php View::includeFile('user-login-header.php');?>

  <?php View::alerts()?>
  <div class="w-100 h-100 d-flex justify-content-center align-items-center">
    <div class="col-5 border border-2 p-5 rounded">
      <div class="text-center">
        <div>
          <img src="<?=View::thumb(Config::get('login_logo')??'assets/gila-logo.png')?>" style="max-height:4em">
        </div>
        <h3><?=__('Register')?></h3>
      </div>

      <form method="post" action="<?=Config::url('user/register?submited')?>" class="g-form">
        <?=Form::hiddenInput('register')?>
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
      <p style="text-align:center">
        <a href="<?=Config::url('user')?>" rel="nofollow"><?=__('Log In')?></a>
      </p>
      <p style="text-align:center">
        <?=__('_register_agree_text')?>
      </p>
    </div>
  </div>

</body>

</html>
