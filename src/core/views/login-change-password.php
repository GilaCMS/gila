<!DOCTYPE html>
<html lang="<?=Gila::config('language')?>">

<head>
  <base href="<?=Gila::base_url()?>">
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title><?=Gila::config('title')?> - <?=__('reset_pass')?></title>

  <?=View::css('core/gila.min.css')?>
  <?=View::css('lib/font-awesome/css/font-awesome.min.css')?>
</head>

<body>
  <?=View::alerts()?>
  <div class="gl-4 centered">

    <div class="border-buttom-main_ text-align-center">
      <div style="width:16%;display:inline-block">
        <img src="<?=Gila::config('admin_logo')?:'assets/gila-logo.png'?>">
      </div>
      <h3><?=__('reset_pass')?></h3>
    </div>

    <form role="form" method="post" action="<?=Gila::base_url()?>login/password_reset" class="g-card g-form wrapper">
      <p><?=__('reset_pass_msg')?><p>
      <div class="form-group">
        <input class="form-control fullwidth" name="email" type="email" autofocus required>
      </div>
      <input type="submit" class="btn btn-primary btn-block" value="<?=__('Send Email')?>">
    </form>
    <p>
      <a href="login"><?=__('Log In')?></a>
    </p>
  </div>

</body>

</html>
