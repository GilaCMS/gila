<!DOCTYPE html>
<html lang="<?=Gila::config('language')?>">

<head>
    <base href="<?=Gila::base_url()?>">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?=Gila::config('title')?> - <?=__('Register')?></title>

    <link href="lib/gila.min.css" rel="stylesheet">
    <link href="lib/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <?php Event::fire('register.head')?>
</head>

<body>
    <?php View::alerts()?>
    <div class="gl-4 centered">
        <div class="border-buttom-main_ text-align-center">
            <div style="width:16%;display:inline-block">
                <img src="<?=Gila::config('admin_logo')?:'assets/gila-logo.png'?>">
            </div>
            <h3><?=__('Register')?></h3>
        </div>

        <form role="form" method="post" action="" class="g-form wrapper g-card">
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
            <?php Event::fire('recaptcha.form')?>
            <input type="submit" class="btn btn-primary btn-block" value="<?=__('Register')?>">
        </form>
        <p>
            <a href="login"><?=__('Log In')?></a>
        </p>
    </div>

</body>

</html>
