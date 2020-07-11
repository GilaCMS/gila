<?php
use Gila\Gila;

Gila::controller('blocks', 'blocks/controllers/blocks', 'blocks');

Gila::contentInit('page', function (&$table) {
  $table['commands'][]='blocks';
  $table['js'][]='src/blocks/assets/blocks_btn.js';
  $table['command']['blocks'] = ['link'=>'blocks/page/'];
});
Gila::contentInit('post', function (&$table) {
  $table['commands'][]='blocks';
  $table['js'][]='src/blocks/assets/blocks_btn.js';
  $table['command']['blocks'] = ['link'=>'blocks/post/'];
});

Gila::widgets([
  'side-cto'=>'blocks/widgets/side-cto',
  'side-image'=>'blocks/widgets/side-image',
  'cards'=>'blocks/widgets/cards',
  'items-list'=>'blocks/widgets/items-list',
  'items-grid'=>'blocks/widgets/items-grid',
  'faq'=>'blocks/widgets/faq'
]);
