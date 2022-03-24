<?php

Config::addList('chartjs-query', [
  'label' => 'Posts: Categories Published',
  'query' => 'SELECT
(SELECT title FROM postcategory x WHERE x.id=postmeta.`value`) a,
(SELECT CASE WHEN publish=0 THEN "Draft" ELSE "Published" END FROM post y WHERE y.id=postmeta.post_id) b,
COUNT(*)
FROM postmeta
WHERE  vartype="category"
GROUP BY a,b;'
]);

if (in_array('shop', Config::packages())) {
  Config::addList('chartjs-query', [
    'label' => __('Shop: Inventory status', ['es'=>'Estado de inventario']),
    'query' => 'SELECT
(SELECT title FROM shop_category x WHERE x.id=shop_productmeta.metavalue) a,
"Stock" b,
SUM(qty)
FROM shop_productmeta, shop_stock
WHERE metakey="category" AND shop_productmeta.product_id = shop_stock.product_id
GROUP BY a,b;'
  ]);
}
