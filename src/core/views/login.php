<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?=gila::config('base')?>">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Gila CMS - Login</title>

    <link href="lib/gila.min.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="lib/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

</head>

<body>

    <div class="gl-4 centered">
        <div class="border-buttom-main_ text-align-center">
            <div style="width:16%;display:inline-block">
                <img src="assets/gila-logo.svg">
            </div>
            <h3>Login</h3>
        </div>

        <form role="form" method="post" action="" class="g-form wrapper g-card">
                <div class="form-group">
                    <input class="form-control fullwidth" placeholder="E-mail" name="username" type="email" autofocus>
                </div>
                <div class="form-group ">
                    <input class="form-control fullwidth" placeholder="Password" name="password" type="password" value="">
                </div>
                <input type="submit" class="btn btn-primary btn-block" value="Login">
                <?php event::fire('login.btn'); ?>
        </form>
        <p>
            <a href="login/password_reset">Forgot password?</a>
            <?php if(gila::config('new_register')) echo '| <a href="login/register">Register</a>';?>
        </p>
    </div>
    <!--div class="checkbox">
        <label>
            <input name="remember" type="checkbox" value="Remember Me">Remember Me
        </label>
    </div-->

</body>

</html>
