<head>
	<link type="text/css" rel="stylesheet" href="../lib/gila.min.css"/>
	<title>Install Gila CMS</title>
</head>
<body class="bg-lightgrey">
    <div class="gm-6 centered row" style="">
        <div class="gm-12 wrapper text-align-center">
            <h1 class="margin-0">Gila CMS Requirements</h1>
        </div>
<?php

if(version_compare(phpversion(), $required_php) < 0) {
    echo "<span class='alert fullwidth'>PHP version 7 is required.</span>";
}else {
    echo "<span class='alert success fullwidth'>PHP version is 7 or more.</span>";
}

foreach($required_ext as $k=>$v) if(!extension_loaded($v)) {
    echo "<span class='alert fullwidth'>Extension $v is not loaded.</span>";
} else {
    echo "<span class='alert success fullwidth'>Extension $v is loaded.</span>";
}
?>
        <p>
            Before you continue to the installation make sure you know the Database name and the user credencials. If you dont know them, ask them from your hosting provider.
        </p>
        <div class="gl-12"><a class="g-btn gl-12" href="install.php">Continue</a></div>
    </div>
</body>
