<?php
header('Content-Type: application/rss+xml; charset=utf-8');
echo '<?xml version="1.0" encoding="utf-8"?>';
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
<title><?=$title?></title>
<link><?=$link?></link>
<description><?=$description?></description>
<atom:link href="<?=Config::get('base')?>rss" rel="self" type="application/rss+xml" />
<image>
  <url><?=Config::get('base')?><?=Config::get('admin_logo')??'assets/gila-logo.png'?></url>
  <title><?=$title?></title>
  <link><?=Config::get('base')?></link>
</image>
<?php foreach ($items as $item) {
  $item = (object)$item; ?>
<item>
<title><?=$item->title?></title>
  <link><?=Config::get('base').'blog/'.$item->id.'/'.$item->slug?></link>
  <guid><?=Config::get('base').'blog/'.$item->id?></guid>
  <pubDate><?=date('r', strtotime($item->updated))?></pubDate>
  <description><![CDATA[<?=$item->post?>]]></description>
</item>
<?php
} ?>
</channel>
</rss>
