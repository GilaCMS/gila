
<?php
echo $c->hola2;
foreach ($c->contenttype as $ct) { ?>
    <div class="box"><div class="box bordered"><?=$ct?></div></div>
<?php
}