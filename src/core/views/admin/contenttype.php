<style>#main-wrapper>div{background: inherit !important;border:none}</style>
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));grid-gap:20px">
<?php
foreach (Config::$content as $key=>$ct) { ?>
    <div class="g-card wrapper bg-white" style="font-size:120%;text-align:center;">
        <a href="admin/content/<?=$key?>">
            <i class="fa fa-3x fa-table"></i><br>
            <span><?=ucfirst($key)?></span>
        </a>
    </div>
<?php
}
?>
</div>
