<?php Gila\View::includeFile('login-header.php');?>

  <div class="gl-4 centered wrapper g-card bg-white">
    <div class="g-form">
      <div class="border-buttom-main_ text-align-center">
        <div style="width:16%;display:inline-block">
          <i class="fa fa-5x fa-check" style="color:green"></i>
        </div>
        <h3><?=__('activate_success')?></h3>
      </div>
      <a class="btn btn-success btn-block" href="<?=Gila::url('login')?>"><?=__('Log In')?></a>
    </div>
  </div>

</body>

</html>
