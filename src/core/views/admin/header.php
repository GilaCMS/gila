<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?=gila::config('base')?>">

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, shrink-to-fit=no, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" href="assets/gila-logo.png">

    <title>Gila CMS - Administration</title>

    <!-- Bootstrap Core CSS -->
    <!--link href="lib/bootstrap/bootstrap.min.css" rel="stylesheet"-->
    <link href="lib/font-awesome/css/font-awesome.min.css" rel="stylesheet" async>

    <!-- Custom CSS -->
    <link href="src/core/assets/simple-sidebar.css" rel="stylesheet" async>
    <link href="lib/gila.min.css" rel="stylesheet">
    <style>
    #sidebar-wrapper .g-nav li{ color:#fff; }
    #sidebar-wrapper .g-nav li a{ color:#aaa; }
    #sidebar-wrapper .g-nav li ul li a{ color:#444; }
    #sidebar-wrapper .g-nav li a:hover{ background:var(--main-dark-color);color:white }
    .g-nav li ul{min-width: 200px}
    .dark-orange li ul{ background-color: #fff; }
    .dark-orange li ul li{ color: var(--main-color); }
    .dark-orange li ul li a{ color: var(--main-color); }
    .dark-orange li ul li a:hover{ color:white; }
    </style>

    <script src="lib/jquery/jquery-2.2.4.min.js"></script>
    <script src="lib/gila.min.js"></script>


</head>

<body style="background:#f5f5f5">

    <div id="wrapper">

        <!-- Sidebar g-nav vertical -->
        <div id="sidebar-wrapper">
            <div style="position: relative;height: 100px;">
                <a href="admin">
                    <img style="width:60px" src="assets/gila-logo.svg" class="centered">
                </a>
            </div>
            <ul class="g-nav vertical">
                <?php
                    foreach (gila::$amenu as $key => $value) {
                        if(isset($value['access'])) if(!gila::hasPrivilege($value['access'])) continue;
                        echo "<li><a href='".gila::url($value[1])."'><i class='fa fa-{$value['icon']}'></i> {$value[0]}</a>";
                        if(isset($value['children'])) {
                            echo "<ul class=\"dropdown\">";
                            foreach ($value['children'] as $subkey => $subvalue) {
                                if(isset($subvalue['access'])) if(!gila::hasPrivilege($subvalue['access'])) continue;
                                echo "<li><a href='".gila::url($subvalue[1])."'><i class='fa fa-{$subvalue['icon']}'></i> {$subvalue[0]}</a></li>";
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
        <div class="g-group fullwidth bordered" style="vertical-align:baseline; background:white;">
                <a href="#menu-toggle" class="btn btn-white g-group-item" id="menu-toggle" title="Toggle Menu"><i class='fa fa-bars'></i></a>
                <a href="<?=gila::config('base')?>" class="btn btn-white g-group-item" title="Homepage" target="_blank"><i class='fa fa-home'></i></a>
            <span class="g-group-item fullwidth text-align-right pad">


                <ul class="g-nav">
                <li ><i class="fa fa-user"></i> <?=session::key('user_name')?> <i class="fa fa-angle-down"></i>
                    <ul class="text-align-left" style="right:0">
                        <li><a href="<?=gila::config('base')?>admin/profile">My Profile</a></li>
                        <li><a href="<?=gila::config('base')?>admin/logout">Logout</a></li>
                    </ul>
                </li>
                </ul>
            </span>
        </div>
        <div class="col-md-12">

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
            <div class="wrapper" id='main-wrapper'>
