<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?=gila::config('base')?>">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Gila CMS - Login</title>

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
            <h3>Register</h3>
        </div>

        <form role="form" method="post" action="" class="g-form wrapper g-card">
            <label>Name</label>
            <div class="form-group">
                <input class="form-control fullwidth" placeholder="name" name="name" autofocus required>
            </div>
            <label>E-mail</label>
            <div class="form-group">
                <input class="form-control fullwidth" placeholder="E-mail" name="email" type="email" required>
            </div>
            <label>Password</label>
            <div class="form-group ">
                <input class="form-control fullwidth" placeholder="Password" name="password" type="password" value="" required>
            </div>
            <?php event::fire('recapcha.form')?>
            <input type="submit" class="btn btn-primary btn-block" value="Register">
        </form>
        <p>
            <a href="login">Login</a>
        </p>
    </div>

</body>

</html>
