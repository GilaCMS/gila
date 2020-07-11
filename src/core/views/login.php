<?php View::includeFile('login-header.php');?>

  <div class="gl-4 centered wrapper g-card bg-white">
    <div class="border-buttom-main_ text-align-center">
      <div>
        <img src="<?=Gila::config('admin_logo')?:'assets/gila-logo.png'?>" style="max-height:4em">
      </div>
      <h3><?=__('Log In')?></h3>
    </div>
    <?=View::alerts()?>
<?php
if (Session::waitForLogin()==0) { ?>
  <form role="form" method="post" action="" class="g-form">
    <div class="form-group">
      <input class="form-control fullwidth" placeholder="E-mail" name="username" type="email" autofocus>
    </div>
    <div class="form-group ">
      <input class="form-control fullwidth" placeholder="Password" name="password" id="pass" type="password" value="">
    </div>
    <input type="submit" class="btn btn-primary btn-block" value="<?=__('Login')?>">
    <?php Event::fire('login.btn'); ?>
  </form>
  <label class="helptext"><input type="checkbox" oninput="if(this.checked) pass.type='text'; else pass.type='password';"> <?=__('Show password')?></label>
<?php } ?>
    <p>
      <a href="login/password_reset"><?=__('forgot_pass')?></a>
      <?php if (Gila::config('user_register')==1) {
  echo '| <a href="login/register">'.__('Register').'</a>';
}?>
    </p>
  </div>

</body>

</html>
