
Posts x Category
```
SELECT
title,'Posts x Category',(SELECT COUNT(*) FROM postmeta where vartype='category' and `value`=postcategory.id)
FROM postcategory;
```

Public Posts x Category
```
SELECT
(SELECT title FROM postcategory x WHERE x.id=postmeta.`value`) a,
(SELECT CASE WHEN publish=0 THEN "Draft" ELSE "Published" END FROM post y WHERE y.id=postmeta.post_id) b,
COUNT(*)
FROM postmeta
WHERE  vartype='category'
GROUP BY a,b;
```

Updated posts
```
SELECT
date_format(updated, '%Y-%m-%d') a,
'Updated Posts',
COUNT(*)
FROM post
GROUP BY a;
```
