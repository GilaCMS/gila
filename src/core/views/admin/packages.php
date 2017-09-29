<table class='g-table'>
<?php
foreach ($packages as $p) { ?>
<tr>
    <td style="width:100%"><h4><?=$p->title?> <?=$p->version?>
    </h4><?=($p->description==''?'No description':$p->description)?>
    <br><b>Author:</b> <?=($p->author!=''?$p->author:'')?>
    <?=($p->url!=''?' <b>Url:</b> <a href="'.$p->url.'" target="_blank">'.$p->url.'</a>':'');


    if (file_exists('src/'.$p->package)) {
        if (in_array($p->package,$GLOBALS['config']['packages'])) {
            echo "<td><a onclick='addon_options(\"{$p->package}\")' class='g-btn' style='display:inline-flex'><i class='fa fa-gears'></i>&nbsp;Options</a><td>";
            echo "<a onclick='addon_deactivate(\"{$p->package}\")' class='g-btn error'>Deactivate</a>";
        } else {
            if($p->package=='core') {
                echo "<td><td>";
            } else echo "<td><td><a onclick='addon_activate(\"{$p->package}\")' class='g-btn success'>Activate</a>";
        }
        $current_version = json_decode(file_get_contents('src/'.$p->package.'/package.json'))->version;
        if(version_compare($p->version,$current_version)>0) echo " <a onclick='addon_download(\"{$p->package}\")' class='g-btn success'>Upgrade</a>";
    } else {
        echo "<td><td><a onclick='addon_download(\"{$p->package}\")' class='g-btn success'>Download</a>";
    }
}
?>
</table>
