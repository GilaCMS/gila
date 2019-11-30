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

    <div class="gl-4 centered">
        <div class="g-form wrapper g-card">
            <div class="border-buttom-main_ text-align-center">
                <div style="width:16%;display:inline-block">
                    <i class="fa fa-5x fa-check" style="color:green"></i>
                </div>
                <h3><?=__('reset_pass_success')?></h3>
            </div>

            <a class="btn btn-success btn-block" href="<?=gila::url('login')?>"><?=__('Log In')?></a>
        </div>
    </div>

</body>

</html>
