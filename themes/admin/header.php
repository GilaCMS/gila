<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?=gila::config('base')?>">

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, shrink-to-fit=no, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Gila CMS - Administration</title>

    <!-- Bootstrap Core CSS -->
    <link href="libs/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="libs/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="themes/admin/simple-sidebar.css" rel="stylesheet">
    <link href="libs/rj.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>

    <div id="wrapper">

        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
                <li class="sidebar-brand">
                    <a href="admin">Admin</a>
                </li>
                <?php
                    foreach ($GLOBALS['menu']['admin'] as $key => $value) {
                        echo "<li><a href='{$value[1]}'><i class='fa fa-{$value['icon']}'></i> {$value[0]}</a></li>";
                    }
                ?>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div class="col-md-12">
            <ul class="gl-nav">
                <li>
                    <a href="#menu-toggle" class="btn btn-default" id="menu-toggle" title="Toggle Menu"><i class='fa fa-bars'></i></a>
                </li>
                <li>
                    <a href="<?=gila::config('base')?>" class="btn btn-default" title="Homepage" target="_blank"><i class='fa fa-home'></i></a>
                </li>
                <li class="float-right">
                    User Name
                    <a href="<?=gila::config('base')?>/logout">Logout</a>
                </li>

            </ul>
            <div style="background:#ddd; padding:6px" class="row">
                <div style="font-size:22px; padding-left: 15px;">
                    <?php
                    $cn = router::controller();
                    $c = new $cn();
                    if (isset($c->icons)) {
                        if (isset($c->icons[router::action()]))
                            echo "<i class='fa fa-{$c->icons[router::action()]}'></i> ";
                    }
                     ?>
                    <?=ucwords(router::action())?>
                </div>
            </div>
            <div class="row">
