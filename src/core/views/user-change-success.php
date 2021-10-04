<?php View::includeFile('user-login-header.php');?>

  <div class="gl-4 centered">
    <div class="g-form wrapper g-card bg-white">
      <div class="border-buttom-main_ text-align-center">
        <div>
          <i class="fa fa-5x fa-check" style="color:green"></i>
        </div>
        <h3><?=__('reset_pass_success')?></h3>
      </div>
      <a class="btn btn-success btn-block" href="<?=Config::url($login_link??'user')?>"><?=__('Log In')?></a>
    </div>
  </div>

</body>

</html>
