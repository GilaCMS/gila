<?php
header('Content-Type: application/rss+xml; charset=utf-8');
echo '<?xml version="1.0" encoding="utf-8"?>';
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
<title><?=$title?></title>
<link><?=$link?></link>
<description><?=$description?></description>
<atom:link href="<?=Config::config('base')?>rss" rel="self" type="application/rss+xml" />
<image>
  <url><?=Config::config('base')?><?=Config::config('admin_logo')?:'assets/gila-logo.png'?></url>
  <title><?=$title?></title>
  <link><?=Config::config('base')?></link>
</image>
<?php foreach ($items as $item) {
  $item = (object)$item; ?>
<item>
<title><?=$item->title?></title>
  <link><?=Config::config('base').'blog/'.$item->id?></link>
  <guid><?=Config::config('base').'blog/'.$item->slug?></guid>
  <pubDate><?=date('r', strtotime($item->updated))?></pubDate>
  <description><![CDATA[<?=$item->post?>]]></description>
</item>
<?php
} ?>
</channel>
</rss>

<?php
//date("D, d M Y H:i:s T", strtotime($item->updated))
//<description><?=strip_tags($item->post)></description>
//  <content:encoded><![CDATA[<?=$item->post>]]></content:encoded>
?>
