<?php

$loremData = <<<EOT
<div class="gm-grid" style="display:grid; grid-gap:1em; grid-template-columns:repeat(auto-fit, minmax(300px,1fr));justify-items:center;margin-bottom:1em;overflow: auto;">
<div class="g-card wrapper bg-white" style="text-align:center;max-width:300px"><div class="g-card-image" style="padding:0 25%"><img src="assets/core/cogs.png"></div><h4 style="margin:2px"><b>Card</b></h4><p>Text</p></div>
<div class="g-card wrapper bg-white" style="text-align:center;max-width:300px"><div class="g-card-image" style="padding:0 25%"><img src="assets/core/cogs.png"></div><h4 style="margin:2px"><b>Card</b></h4><p>Text</p></div>
<div class="g-card wrapper bg-white" style="text-align:center;max-width:300px"><div class="g-card-image" style="padding:0 25%"><img src="assets/core/cogs.png"></div><h4 style="margin:2px"><b>Card</b></h4><p>Text</p></div>
</div>
EOT;

return [
    'text'=>[
        'type'=>'paragraph',
        'allow_tags'=>true,
        'default'=>$loremData
    ]
];
