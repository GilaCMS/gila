<?php

// Remove this on next version
if (Config::config('page-blocks')===null) {
  Router::controller('blocks', 'blocks/controllers/blocks', 'blocks');

  Config::contentInit('page', function (&$table) {
    $table['commands'][]='blocks_popup';
    $table['js'][]='src/blocks/assets/admin/blocks_btn.js';
    $table['command']['blocks'] = ['link'=>'blocks/page/'];
  });
  
  Config::contentInit('post', function (&$table) {
    $table['commands'][]='blocks';
    $table['js'][]='src/core/assets/admin/blocks_btn.js';
    $table['command']['blocks'] = ['link'=>'blocks/post/'];
  });
}

Config::widgets([
  'text--header'=>'blocks/widgets/text--header',
  'text--faq'=>'blocks/widgets/text--faq',
  'side-image'=>'blocks/widgets/side-image',
  'side-image--items'=>'blocks/widgets/side-image--items',
  'side-cto'=>'blocks/widgets/side-cto', // DEPRECATED
  'cards'=>'blocks/widgets/cards',
  'items-list'=>'blocks/widgets/items-list', // DEPRECATED
  'faq'=>'blocks/widgets/faq', // DEPRECATED
  'items-grid'=>'blocks/widgets/items-grid',
  'image-overlay'=>'blocks/widgets/image-overlay',
  //'image-slider'=>'blocks/widgets/image-slider',
  //'hero-slider'=>'blocks/widgets/hero-slider',
]);
