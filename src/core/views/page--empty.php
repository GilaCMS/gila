<!DOCTYPE html>
<html lang="<?=Config::lang()?>">
<?php View::stylesheet('core/gila.min.css') ?>
<?php View::head()?>
<body>
<div style="margin:0">
<?=$text?>
</div>
<?php View::scriptAsync("core/gila.min.js")?>
</body>
</html>
