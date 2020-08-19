<?php

Router::controller('blocks', 'blocks/controllers/blocks', 'blocks');

Config::contentInit('page', function (&$table) {
  $table['commands'][]='blocks_popup';
  $table['js'][]='src/blocks/assets/blocks_btn.js';
  $table['command']['blocks'] = ['link'=>'blocks/page/'];
});
Config::contentInit('post', function (&$table) {
  $table['commands'][]='blocks';
  $table['js'][]='src/blocks/assets/blocks_btn.js';
  $table['command']['blocks'] = ['link'=>'blocks/post/'];
});

Config::widgets([
  'side-cto'=>'blocks/widgets/side-cto',
  'side-image'=>'blocks/widgets/side-image',
  'cards'=>'blocks/widgets/cards',
  'items-list'=>'blocks/widgets/items-list',
  'items-grid'=>'blocks/widgets/items-grid',
  'faq'=>'blocks/widgets/faq'
]);
