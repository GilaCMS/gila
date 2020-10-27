
<head>
	<base href="<?=Gila\Config::base()?>">
	<title>Gila CMS</title>
	<style><?=file_get_contents("src/core/assets/gila.min.css")?></style>
</head>
<body class="bg-lightgrey">
<div class="gm-6 centered row" style="">
    <div class="gm-12 wrapper text-align-center">
        <h1 class="margin-0">Installation finished successfully!</h1>
    </div>
<div class="row gap-16px bordered box-shadow g-form bg-white">
	<a class="gl-12 g-btn success" href="<?=$GLOBALS['config']['base']?>" target="_blank">Visit the website</a>
	<a class="gl-12 g-btn" href="<?=$GLOBALS['config']['base']?>?c=admin" target="_blank">Login to admin panel</a>
</div>
</div>
