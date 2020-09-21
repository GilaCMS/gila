<?php

Router::controller('blocks', 'blocks/controllers/blocks', 'blocks');

Config::contentInit('page', function (&$table) {
  $table['commands'][]='blocks_popup';
  $table['js'][]='src/blocks/assets/blocks_btn.js';
  $table['command']['blocks'] = ['link'=>'blocks/page/'];
});
if(Config::config('page-blocks')===null) Config::contentInit('post', function (&$table) {
  $table['commands'][]='blocks';
  $table['js'][]='src/blocks/assets/blocks_btn.js';
  $table['command']['blocks'] = ['link'=>'blocks/post/'];
});

Config::widgets([
  'paragraph--header'=>'blocks/widgets/paragraph--header',
  'paragraph--faq'=>'blocks/widgets/paragraph--faq',
  'side-image'=>'blocks/widgets/side-image',
  'side-image--items'=>'blocks/widgets/side-image--items',
  'side-image--cto'=>'blocks/widgets/side-image--cto',
  'side-cto'=>'blocks/widgets/side-cto', // DEPRECATED
  'cards'=>'blocks/widgets/cards',
  'items-list'=>'blocks/widgets/items-list', // DEPRECATED
  'faq'=>'blocks/widgets/faq', // DEPRECATED
  'items-grid'=>'blocks/widgets/items-grid',
  'image-overlay'=>'blocks/widgets/image-overlay',
  'image-slider'=>'blocks/widgets/image-slider',
]);
