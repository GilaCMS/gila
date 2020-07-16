<head>
  <base href="<?=Gila\Config::base_url()?>">
  <title>Install Gila CMS</title>
  <style><?=file_get_contents("src/core/assets/gila.min.css")?></style>
</head>
<body class="bg-lightgrey">
<div class="gm-6 centered row" style="">
  <div class="gm-12 wrapper text-align-center">
      <h1 class="margin-0">Gila CMS Installation</h1>
  </div>

  <form method="post" action="?install&step=1" class="row gap-16px bordered box-shadow g-form bg-white">
    <div class="gl-6">
    <label class="gs-12">DB Hostname</label>
    <input name="db_host" value="<?=htmlentities($_POST['db_host']??'localhost')?>" placeholder="Hostname" required>
    <label class="gs-12">DB Name</label>
    <input name="db_name" value="<?=htmlentities($_POST['db_name']??'')?>" required>
    <label class="gs-12">DB User</label>
    <input name="db_user" value="<?=htmlentities($_POST['db_user']??'')?>" required>
    <label class="gs-12">DB Password</label>
    <input name="db_pass" type="password">
    </div>
    <div class="gl-6">
    <label class="gs-12">Admin Name</label>
    <input name="adm_user" placeholder="Your Name" value="<?=htmlentities($_POST['adm_user']??'')?>" required>
    <label class="gs-12">Admin Email</label>
    <input name="adm_email" type="email" placeholder="Your Email" value="<?=htmlentities($_POST['adm_email']??'')?>" required>
    <label class="gs-12">Admin Password</label>
    <input name="adm_pass" type="password" placeholder="Choose A Password" required>
    <label class="gs-12">Base URL</label>
    <input name="base_url" value="<?=htmlentities($_POST['base_url']??Gila\Config::base_url())?>" placeholder="https://www.mysite.com/" required>
    </div>
    <div class="gl-12"><input class="btn success" type="submit" value="Install"></div>
  </form>
  <p>
    Checkout the <a href="https://gila-cms.readthedocs.io/en/latest/install.html#errors" target="_blank">documentation</a>.<br>
    If you have difficulties to finish the installation you can ask for help on <a href="https://gitter.im/GilaCMS/Lobby" target="_blank">Gitter</a>
  </p>
</div>
