<?php
$config_list = ['title'=>'Title', 'slogan'=>'Description', 'base'=>'Website URL', 'admin_email'=>'Admin Email'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') if (router::post('submit-btn')=='submited'){
    $_lc=substr($_POST['gila_base'],-1);
    if($_lc!='/' && $_lc!='\\') $_POST['gila_base'].='/';
    foreach ($config_list as $key => $value) {
        gila::setConfig($key,$_POST['gila_'.$key]);
    }
    gila::config('theme',$_POST['gila_theme']);
    gila::config('default-controller',$_POST['gila_dc']);
    gila::config('timezone',$_POST['gila_timezone']);
    gila::config('language',$_POST['gila_language']);
    gila::config('env',$_POST['gila_env']);
    gila::config('rewrite',$_POST['gila_rewrite']);
    gila::config('user_register',$_POST['gila_user_register']);
    gila::config('default.menu',$_POST['gila_defaultmenu']);
    gila::updateConfigFile();
    view::alert('success','Changes have been saved successfully!');
}
view::script('src/core/assets/admin/media.js');
?>

<div class="gm-12">
    <?php view::alerts(); ?>
    <form method="post" action="<?=gila::make_url('admin','settings')?>" class="g-form">

<?php

foreach ($config_list as $key=>$value) if($value[0] != '.') { ?>
    <br><div class="gm-12">
    <label class="gm-3"><?=__($value)?></label>
    <input name="gila_<?=$key?>" value="<?=gila::config($key)?>" class="gm-4" />
    </div>
<?php } ?>

<br><div class="gm-12">
<label class="gm-3"><?=__("Pretty Urls")?></label>
<select name="gila_rewrite" class="gm-4">
<?php
$purls = [true=>'Yes',false=>'No'];
foreach ($purls as $k=>$value) {
    $sel = (gila::config('rewrite')==$k?'selected':'');
    echo '<option value="'.$k."\" $sel>".$value.'</option>';
}
?>
</select>
</div>

<br><div class="gm-12">
<label class="gm-3"><?=__("Main Menu")?></label>
<select name="gila_defaultmenu" class="gm-4">
<?php
$res = core\models\widget::getByWidget("menu");
$sel_wm = gila::config('default.menu');
echo '<option value="0">(default)';
foreach ($res as $k=>$wm) {
    $sel = ($sel_wm==$wm['id']?'selected':'');
    echo '<option value="'.$wm['id']."\" $sel>".@$wm['title'];
}
?>
</select>
</div>

<br><div class="gm-12">
<label class="gm-3"><?=__("New users can register")?></label>
<select name="gila_user_register" class="gm-4">
<?php
$sel_urs = [true=>'Yes',false=>'No'];
foreach ($sel_urs as $k=>$value) {
    $sel = (gila::config('user_register')==$k?'selected':'');
    echo '<option value="'.$k."\" $sel>".$value.'</option>';
}
?>
</select>
</div>

    <br><div class="gm-12">
    <label class="gm-3"><?=__("Theme")?></label>
    <select name="gila_theme" value="<?=gila::config('theme')?>" class="gm-4">
    <?php
    $theme_list=scandir('themes/');
    foreach ($theme_list as $value) if($value[0] != '.') if(count(explode('.',$value))==1) {
        $sel = (gila::config('theme')==$value?'selected':'');
        echo '<option value="'.$value."\" $sel>".ucwords($value).'</option>';
    }
    ?>
    </select>
    </div>

    <br><div class="gm-12">
        <label class="gm-3"><?=__("Timezone")?></label>
        <select name="gila_timezone" value="<?=gila::config('timezone')?>" class="gm-4">
        <?php
        foreach (DateTimeZone::listIdentifiers() as $value) {
            $sel = (gila::config('timezone')==$value?'selected':'');
            echo '<option value="'.$value."\" $sel>".ucwords($value).'</option>';
        }
        ?>
        </select>
    </div>

    <br><div class="gm-12">
        <label class="gm-3"><?=__("Language")?></label>
        <select name="gila_language" value="<?=gila::config('language')?>" class="gm-4">
        <?php
        $languages = ['en'=>'English','es'=>'Español','gr'=>'Ελληνικά','et'=>'Eesti'];
        foreach ($languages as $k=>$value) {
            $sel = (gila::config('language')==$k?'selected':'');
            echo '<option value="'.$k."\" $sel>".ucwords($value).'</option>';
        }
        ?>
        </select>
    </div>

    <br><div class="gm-12">
    <label class="gm-3"><?=__("Default Controller")?></label>
    <select name="gila_dc" value="<?=gila::config('default-controller')?>" class="gm-4">
    <?php
    foreach (gila::$controller as $k=>$value) if($value[0] != '.') {
        $sel = (gila::config('default-controller')==$k?'selected':'');
        echo '<option value="'.$k."\" $sel>".ucwords($k).'</option>';
    }
    ?>
    </select>
    </div>

    <br><div class="gm-12">
    <label class="gm-3"><?=__("Environment")?></label>
    <select name="gila_env" class="gm-4">
    <?php
    $environments = ['pro'=>'Production','dev'=>'Development'];
    foreach ($environments as $k=>$value) {
        $sel = (gila::config('env')==$k?'selected':'');
        echo '<option value="'.$k."\" $sel>".ucwords($value).'</option>';
    }
    ?>
    </select>
    </div>

    <br><input type="submit" name="submit-btn" onclick="this.value='submited'"
    class="btn btn-primary col-md-1 col-md-offset-1 gm-1"  />
    </form>
</div>
