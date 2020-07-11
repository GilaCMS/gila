<?php
require_once('src/core/classes/TableSchema.php');
TableSchema::update(include 'src/core/tables/post.php');

$db->query('ALTER TABLE post ADD  FULLTEXT KEY `title` (`title`,`post`);');

TableSchema::update(include 'src/core/tables/postcategory.php');

TableSchema::update(include 'src/core/tables/page.php');

TableSchema::update(include 'src/core/tables/user.php');

$db->query('CREATE TABLE IF NOT EXISTS `widget` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `widget` varchar(80) DEFAULT NULL,
  `title` varchar(80) DEFAULT NULL,
  `area` varchar(80) DEFAULT NULL,
  `active` int(1) DEFAULT 1,
  `pos` int(2) DEFAULT 0,
  `data` text,
  PRIMARY KEY (`id`),
  KEY `area` (`area`),
  KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

$db->query('CREATE TABLE IF NOT EXISTS `option` (
  `option` varchar(80) NOT NULL,
  `value` text DEFAULT NULL,
  PRIMARY KEY (`option`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

TableSchema::update(include 'src/core/tables/userrole.php');

$_user = $_POST['adm_user'];
$_email = $_POST['adm_email'];
$_pass = password_hash($_POST['adm_pass'], PASSWORD_BCRYPT);

$db->query("INSERT INTO userrole(id,userrole) VALUES(1,'Admin');");
$db->query("INSERT INTO user(id,username,email,pass,active,reset_code)
  VALUES(1,?,?,?,1,'');", [$_user,$_email,$_pass]);
$db->query("INSERT INTO usermeta VALUES(1,1,'role',1);");
$db->query("INSERT INTO post(id,user_id,title,slug,description,post,publish,updated)
  VALUES(1,1,'Hello World','hello_world','This is the first post. You can edit it from administration.','This is the first post',1,CURRENT_TIMESTAMP);");
$db->query("INSERT INTO page(id,title,slug,content,publish,updated,template)
  VALUES(1,'About','about','This is a page to describe your website',1,CURRENT_TIMESTAMP,'');");

// preinstall widgets on dashboard
$wtext1 = '{"text":"<ol><li><a href=\\\"admin\\\/content\\\/postcategory\\\">Create Categories<\\\/a><\\\/li><li><a href=\\\"admin\\\/content\\\/page\\\">Edit About Page<\\\/a><\\\/li><li><a href=\\\"admin\\\/content\\\/post\\\">Create Posts<\\\/a><\\\/li><li><a href=\\\"admin\\\/media\\\">Upload Images<\\\/a><\\\/li><li><a href=\\\"admin\\\/settings\\\">Set Basic Settings<\\\/a><\\\/li><\\\/ol>"}';
$wtext2 = '{"text":"<ul><li><a href=\\\\\"https:\\\/\\\/www.facebook.com\\\/gilacms\\\/\\\\\" target=\\\\\"_blank\\\\\">Facebook Page<\\\/a><\\\/li><li><a href=\\\\\"https:\\\/\\\/twitter.com\\\/GilaCms\\\\\" target=\\\\\"_blank\\\\\">Retweet<\\\/a> us!<\\\/li><li>Give a star on <a href=\\\\\"https:\\\/\\\/github.com\\\/GilaCMS\\\/gila\\\\\" target=\\\\\"_blank\\\\\">Github<\\\/a><\\\/li><li>Review on <a href=\\\\\"https:\\\/\\\/sourceforge.net\\\/projects\\\/gila-cms\\\/reviews\\\/new\\\\\" target=\\\\\"_blank\\\\\">SourceForge<\\\/a><\\\/li><li>Like at <a href=\\\"https:\\/\\/alternativeto.net\\/software\\/gila-cms\\/\\\" target=\\\"_blank\\\">AlternativeTo<\\\/a><\\\/li><\\\/ul>"}';
$wtext3 = '{"text":"<ul><li><a href=\\\"https:\\\/\\\/gila-cms.readthedocs.io\\\" target=\\\"_blank\\\">Documentation<\\\/a><\\\/li><li>Join <a href=\\\"https:\\\/\\\/gitter.im\\\/GilaCMS\\\/Lobby\\\" target=\\\\\"_blank\\\">Gitter<\\\/a><\\\/li><li>Subscribe to developers <a href=\\\"https:\\/\\/tinyletter.com\\/gilacms\\\">newsletter<\\/a><\\/li><\\\/ul>"}';

$db->query("INSERT INTO widget(id,widget,title,area,active,pos,data)
VALUES(1,'paragraph','Start Blogging','dashboard',1,1,'".$wtext1."'),
(2,'paragraph','Support GilaCMS','dashboard',1,2,'".$wtext2."'),
(4,'paragraph','Get Help','dashboard',1,3,'".$wtext3."');");
