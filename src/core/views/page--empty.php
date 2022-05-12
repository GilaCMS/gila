<!DOCTYPE html>
<html lang="<?=Config::lang()?>">
<?php View::stylesheet('core/gila.min.css') ?>
<?=View::cssAsync('lib/bootstrap5/bootstrap.min.css')?>
<?php View::head()?>
<body>
<div style="margin:0">
<?=$text?>
</div>
<?php View::scriptAsync("core/gila.min.js")?>
</body>
</html>
