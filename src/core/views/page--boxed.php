<!DOCTYPE html>
<html lang="<?=Config::lang()?>">
<?php View::stylesheet('core/gila.min.css') ?>
<?=View::cssAsync('lib/bootstrap5/bootstrap.min.css')?>
<?php View::head()?>
<body style="background-color:#d8d8d8">
<div style="margin:auto;max-width:700px;margin-top:10%;min-height:2em;background-color:white">
<?=$text?>
</div>
<footer></footer>
<style><?=htmlentities(Config::get('theme.css'))?></style>
<?=View::script('core/gila.min.js');?>
</body>
</html>
