<!DOCTYPE html>
<html lang="<?=Config::get('language')?>">
<?php View::stylesheet('core/gila.min.css') ?>
<?php View::head()?>
<body style="margin:0">
<?=$text?>
<?php View::scriptAsync("core/gila.min.js")?>
</body>
</html>
