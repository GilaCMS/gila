<?php
$config_list = ['title'=>'Title', 'slogan'=>'Description', 'base'=>'Website URL'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') if (router::post('submit-btn')=='submited'){
    $_lc=mb_substr($_POST['gila_base'],-1);
    if($_lc!='/' && $_lc!='\\') $_POST['gila_base'].='/';
    foreach ($config_list as $key => $value) {
        gila::setConfig($key,$_POST['gila_'.$key]);
    }
    gila::config('theme',$_POST['gila_theme']);
    gila::config('default-controller',$_POST['gila_dc']);
    gila::config('timezone',$_POST['gila_timezone']);
    gila::config('env',$_POST['gila_env']);
    if (gila::updateConfigFile())
        echo gila::alert('success','Changes have been saved successfully!');
}
?>

<div class="gm-12">
    <form method="post" action="admin/settings" class="g-form">

<?php

foreach ($config_list as $key=>$value) if($value[0] != '.') { ?>
    <br><div class="gm-12">
    <label class="gm-3"><?=$value?></label>
    <input name="gila_<?=$key?>" value="<?=gila::config($key)?>" class="gm-3" />
    </div>
<?php } ?>


    <br><div class="gm-12">
    <label class="gm-3">Theme</label>
    <select name="gila_theme" value="<?=gila::config('theme')?>" class="gm-3">
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
        <label class="gm-3">Timezone</label>
        <select name="gila_timezone" value="<?=gila::config('timezone')?>" class="gm-3">
        <?php
        foreach (DateTimeZone::listIdentifiers() as $value) {
            $sel = (gila::config('timezone')==$value?'selected':'');
            echo '<option value="'.$value."\" $sel>".ucwords($value).'</option>';
        }
        ?>
        </select>
    </div>

    <br><div class="gm-12">
    <label class="gm-3">Default Controller</label>
    <select name="gila_dc" value="<?=gila::config('default-controller')?>" class="gm-3">
    <?php
    foreach (gila::$controller as $k=>$value) if($value[0] != '.') {
        $sel = (gila::config('default-controller')==$k?'selected':'');
        echo '<option value="'.$k."\" $sel>".ucwords($k).'</option>';
    }
    ?>
    </select>
    </div>

    <br><div class="gm-12">
    <label class="gm-3">Environment</label>
    <select name="gila_env" value="<?=gila::config('default-controller')?>" class="gm-3">
    <?php
    $environments = ['pro'=>'Production','dev'=>'Development'];
    foreach ($environments as $k=>$value) if($value[0] != '.') {
        $sel = (gila::config('env')==$k?'selected':'');
        echo '<option value="'.$k."\" $sel>".ucwords($k).'</option>';
    }
    ?>
    </select>
    </div>

    <br><input type="submit" name="submit-btn" onclick="this.value='submited'"
    class="btn btn-primary col-md-1 col-md-offset-1 gm-1"  />
    </form>
</div>
