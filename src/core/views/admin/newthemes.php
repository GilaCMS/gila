<table class='g-table'>
<?php
$_themes=scandir('themes/');
foreach ($packages as $p) { ?>
<tr>
    <td style="width:33%"><div ><img src="<?=$p->screenshot?>"  /></div>
    <td style="width:100%"><h4><?=$p->title?> <?=$p->version?>
    </h4><?=($p->description==''?'No description':$p->description)?>
    <br><b>Author:</b> <?=($p->author!=''?$p->author:'')?>
    <?=($p->url!=''?' <b>Url:</b> <a href="'.$p->url.'" target="_blank">'.$p->url.'</a>':'');

    if (in_array($p->package,$_themes)) {
        echo "<td>";
        $current_version = json_decode(file_get_contents('themes/'.$p->package.'/package.json'))->version;
        if(version_compare($p->version,$current_version)>0) echo "<a onclick='theme_download(\"{$p->package}\")' class='g-btn success'>Upgrade</a>";
    } else echo "<td><a onclick='theme_download(\"{$p->package}\")' class='g-btn success'>Download</a>";
}
?>
</table>
