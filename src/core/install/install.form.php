<head>
    <base href="<?=gila::base_url()?>">
	<link type="text/css" rel="stylesheet" href="lib/gila.min.css"/>
	<title>Install Gila CMS</title>
</head>
<body class="bg-lightgrey">
<div class="gm-6 centered row" style="">
    <div class="gm-12 wrapper text-align-center">
        <h1 class="margin-0">Gila CMS Installation</h1>
    </div>

<form method="post" action="?install&step=1" class="row gap-16px bordered box-shadow g-form bg-white">
	<div class="gl-6">
	<label class="gs-12">Hostname</label>
	<input name="db_host" value="localhost" placeholder="Hostname" required>
	<label class="gs-12">Database</label>
	<input name="db_name" required>
	<label class="gs-12">DB Username</label>
	<input name="db_user" required>
	<label class="gs-12">DB Password</label>
	<input name="db_pass" type="password">
	</div>
	<div class="gl-6">
	<label class="gs-12">Admin Username</label>
	<input name="adm_user" placeholder="Your Name" required>
	<label class="gs-12">Admin Email</label>
	<input name="adm_email" type="email" placeholder="Your Email" required>
	<label class="gs-12">Admin Password</label>
	<input name="adm_pass" type="password" placeholder="Choose A Password" required>
	<label class="gs-12">Base URL</label>
	<input name="base_url" value="<?=gila::base_url()?>" placeholder="https://www.mysite.com/" required>
	</div>
	<div class="gl-12"><input class="btn success" type="submit"></div>
</form>
<p>If you have difficulties to finish the installation you can ask for help on <a href="https://gitter.im/GilaCMS/Lobby" target="_blank">Forum</a> or
	<a href="https://join.slack.com/t/gilacms/shared_invite/enQtMzU5OTg5OTUzMDQ3LWFiZThjMTliOGQ0NWQyOGRiZTM2ZDlmYTkxODAzZWRlOWJmNmVjNDZkYThjODRjZjdkMmNmOTU3MWUxMDlhZTc" target="_blank">Slack</a></p>
</div>
