<table class='g-table'>
<?php
$_themes=scandir('themes/');
foreach ($packages as $p) { ?>
<tr>
    <td style="width:20%"><div ><img src="<?=$p->screenshot?>"  /></div>
    <td style="width:100%"><h4><?=$p->title?> <?=$p->version?>
    </h4><?=($p->description==''?'No description':$p->description)?>
    <br><b>Author:</b> <?=($p->author!=''?$p->author:'')?>
    <?=($p->url!=''?' <b>Url:</b> <a href="'.$p->url.'" target="_blank">'.$p->url.'</a>':'');

    if ($p->package==gila::config('theme')) {
        echo "<td><a onclick='theme_options(\"{$p->package}\")' class='g-btn' style='display:inline-flex'><i class='fa fa-gears'></i>&nbsp;Options</a><td>";
    }
    else if (in_array($p->package,$_themes)) {
        echo "<td><td><a onclick='theme_activate(\"{$p->package}\")' class='g-btn success'>Select</a>";
        //echo "<a onclick='theme_remove(\"{$p->package}\")' class='g-btn error'><i class=\"fa fa-remove\"></i></a>";
    } else echo "<td><td><a onclick='theme_download(\"{$p->package}\")' class='g-btn success'>Download</a>";
}
?>
</table>
