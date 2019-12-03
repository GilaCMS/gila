<?php

gila::controller('blocks','blocks/controllers/blocks','blocks');

gila::contentInit('page', function(&$table) {
  $table['commands'][]='blocks';
  $table['js'][]='src/blocks/assets/blocks_btn.js';
  $table['command']['blocks'] = ['link'=>'blocks/page/'];
});
gila::contentInit('post', function(&$table) {
    $table['commands'][]='blocks';
    $table['js'][]='src/blocks/assets/blocks_btn.js';
    $table['command']['blocks'] = ['link'=>'blocks/post/'];
});
