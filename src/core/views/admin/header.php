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
    <link href="libs/bootstrap/bootstrap.min.css" rel="stylesheet" async>
    <link href="libs/font-awesome/css/font-awesome.min.css" rel="stylesheet" async>

    <!-- Custom CSS -->
    <link href="src/core/assets/simple-sidebar.css" rel="stylesheet" async>
    <link href="libs/gila.min.css" rel="stylesheet">

    <script src="libs/jquery/jquery-2.2.4.min.js"></script>
    <script src="libs/bootstrap/bootstrap.min.js"></script>
    <script src="libs/gila.min.js"></script>


</head>

<body>

    <div id="wrapper">

        <!-- Sidebar g-nav vertical -->
        <div id="sidebar-wrapper">
            <div style="position: relative;height: 100px;">
                <img style="width:60px" src="install/logo.png" class="centered">
            </div>
            <ul class="g-nav vertical sidebar-nav ">
                <?php
                    foreach (gila::$amenu as $key => $value) {
                        echo "<li><a href='{$value[1]}'><i class='fa fa-{$value['icon']}'></i> {$value[0]}</a>";
                        if(isset($value['children'])) {
                            echo "<ul class=\"dropdown\">";
                            foreach ($value['children'] as $subkey => $subvalue) {
                                echo "<li><a href='{$subvalue[1]}'><i class='fa fa-{$subvalue['icon']}'></i> {$subvalue[0]}</a></li>";
                            }
                            echo "</ul>";
                        }
                        echo "</li>";
                    }
                ?>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div class="col-md-12">
            <ul class="g-toolbar">
                <li>
                    <a href="#menu-toggle" class="btn btn-default" id="menu-toggle" title="Toggle Menu"><i class='fa fa-bars'></i></a>
                </li>
                <li>
                    <a href="<?=gila::config('base')?>" class="btn btn-default" title="Homepage" target="_blank"><i class='fa fa-home'></i></a>
                </li>
                <li class="float-right">
                    User Name
                    <a href="<?=gila::config('base')?>admin/logout">Logout</a>
                </li>

            </ul>
            <div style="background:#ddd; padding:6px" class="row caption">
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
            <div class="wrapper">
