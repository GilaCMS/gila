<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?=gila::config('base')?>">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?=gila::config('title')?> - <?=__('Register')?></title>

    <link href="lib/gila.min.css" rel="stylesheet">
    <link href="lib/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <?php event::fire('register.head')?>
</head>

<body>
    <?php view::alerts()?>
    <div class="gl-4 centered">
        <div class="border-buttom-main_ text-align-center">
            <div style="width:16%;display:inline-block">
                <img src="assets/gila-logo.svg">
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
            <?php event::fire('recapcha.form')?>
            <input type="submit" class="btn btn-primary btn-block" value="<?=__('Register')?>">
        </form>
        <p>
            <a href="login"><?=__('Log In')?></a>
        </p>
    </div>

</body>

</html>
