<!DOCTYPE html>
<html lang="<?=gila::config('language')?>">

<head>
    <base href="<?=gila::base_url()?>">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?=gila::config('title')?> - <?=__('reset_pass')?></title>

    <link href="lib/gila.min.css" rel="stylesheet">
    <link href="lib/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
</head>

<body>
    <?=view::alerts()?>
    <div class="gl-4 centered">

        <div class="border-buttom-main_ text-align-center">
            <div style="width:16%;display:inline-block">
                <img src="<?=gila::config('admin_logo')?:'assets/gila-logo.png'?>">
            </div>
            <h3><?=__('reset_pass')?></h3>
        </div>

        <form role="form" method="post" action="<?=gila::base_url()?>login/password_reset" class="g-card g-form wrapper">
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
