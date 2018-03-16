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
</head>

<body>

    <div class="gl-4 centered">

        <div class="border-buttom-main_ text-align-center">
            <div style="width:16%;display:inline-block">
                <img src="assets/gila-logo.svg">
            </div>
            <h3>Reset Password</h3>
        </div>

        <form role="form" method="post" action="" class="g-card g-form wrapper">
            <p>Submit your new password.<p>
            <div class="form-group">
                <input class="form-control fullwidth" placeholder="New Password" name="pass" type="password" autofocus>
            </div>
            <div class="form-group">
                <input class="form-control fullwidth" placeholder="Confirm Password" name="pass2" type="password" autofocus>
            </div>

            <input type="submit" class="btn btn-primary btn-block" value="Send Email">
        </form>
        <p>
            <a href="login">Login</a>
        </p>
    </div>

</body>

</html>
