<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?=$GLOBALS['path']['base']?>">

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, shrink-to-fit=no, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Gila CMS - Administration</title>

    <!-- Bootstrap Core CSS -->
    <link href="libs/bootstrap/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="src/core/theme/simple-sidebar.css" rel="stylesheet">
    <link href="libs/rj.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
	<script src="libs/rj.js"></script>
</head>

<body>

    <div id="wrapper">

        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
                <li class="sidebar-brand">
                    <a href="admin">Gila Admin</a>
                </li>
                <?php
                    foreach ($GLOBALS['menu']['admin'] as $key => $value) {
                        echo "<li><a href='{$value[1]}'>{$value[0]}</a></li>";
                    }
                ?>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <ul class="top-nav gl-nav">
                <li>
                    <a href="#menu-toggle" class="btn btn-default" id="menu-toggle">Toggle Menu</a>
                </li>
                <li>
                    <a href="<?=$GLOBALS['path']['base']?>">Website</a>
                </li>
                <li class="float-right">
                    User Name
                    <a href="<?=$GLOBALS['path']['base']/logout?>">Logout</a>
                </li>

            </ul>

            <div class="container-fluid">
                <div class="row">
