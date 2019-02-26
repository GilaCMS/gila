<?php
$link->query('CREATE TABLE IF NOT EXISTS `post` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(80) DEFAULT NULL,
  `slug` varchar(80) CHARACTER SET latin1 DEFAULT NULL,
  `description` varchar(200),
  `post` text,
  `publish` int(1) DEFAULT NULL,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `slug` (`slug`),
  KEY `publish` (`publish`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

$link->query('ALTER TABLE post ADD  FULLTEXT KEY `title` (`title`,`post`);');


$link->query('CREATE TABLE IF NOT EXISTS `postmeta` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) DEFAULT NULL,
  `vartype` varchar(80) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

$link->query('CREATE TABLE IF NOT EXISTS `postcategory` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

$link->query('CREATE TABLE IF NOT EXISTS `page` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(80) DEFAULT NULL,
  `slug` varchar(80) CHARACTER SET latin1 DEFAULT NULL,
  `content` text,
  `publish` int(1) DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `slug` (`slug`),
  KEY `publish` (`publish`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

$link->query('CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(80) DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL,
  `pass` varchar(120) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 0,
  `reset_code` varchar(60) DEFAULT NULL,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

$link->query('CREATE TABLE IF NOT EXISTS `usermeta` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `vartype` varchar(80) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `vartype` (`vartype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

$link->query('CREATE TABLE IF NOT EXISTS `widget` (
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

$link->query('CREATE TABLE IF NOT EXISTS `option` (
  `option` varchar(80) NOT NULL,
  `value` text DEFAULT NULL,
  PRIMARY KEY (`option`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

$link->query('CREATE TABLE IF NOT EXISTS `userrole` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userrole` varchar(80) DEFAULT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

$_user=$_POST['adm_user'];
$_email=$_POST['adm_email'];
$_pass=password_hash($_POST['adm_pass'], PASSWORD_BCRYPT);

$link->query("INSERT INTO userrole(id,userrole) VALUES(1,'Admin');");
$link->query("INSERT INTO user(id,username,email,pass,active,reset_code) VALUES(1,'$_user','$_email','$_pass',1,'');");
$link->query("INSERT INTO usermeta VALUES(1,1,'privilege','admin');");
$link->query("INSERT INTO post(id,user_id,title,slug,description,post,publish,updated)
VALUES(1,1,'Hello World','hello_world','This is the first post','This is the first post',1,CURRENT_TIMESTAMP);");
$link->query("INSERT INTO page(id,title,slug,content,publish,updated)
VALUES(1,'About','about','This is a page to describe your website',1,CURRENT_TIMESTAMP);");

// preinstall widgets on dashboard
$wtext1 = '{"text":"<ol><li><a href=\\\"admin\\\/content\\\/postcategory\\\">Create Categories<\\\/a><\\\/li><li><a href=\\\"admin\\\/content\\\/page\\\">Edit About Page<\\\/a><\\\/li><li><a href=\\\"admin\\\/content\\\/post\\\">Create Posts<\\\/a><\\\/li><li><a href=\\\"admin\\\/media\\\">Upload Images<\\\/a><\\\/li><li><a href=\\\"admin\\\/settings\\\">Set Basic Settings<\\\/a><\\\/li><\\\/ol>"}';
$wtext2 = '{"text":"<ul><li><a href=\\\"https:\\\/\\\/gilacms.com\\\/docs\\\" target=\\\"_blank\\\">Documentation<\\\/a><\\\/li><li>Join <a href=\\\"https:\\\/\\\/gitter.im\\\/GilaCMS\\\/Lobby\\\" target=\\\\\"_blank\\\">Gitter<\\\/a><\\\/li><li>Join <a href=\\\"https:\\\/\\\/join.slack.com\\\/t\\\/gilacms\\\/shared_invite\\\/enQtMzU5OTg5OTUzMDQ3LWFiZThjMTliOGQ0NWQyOGRiZTM2ZDlmYTkxODAzZWRlOWJmNmVjNDZkYThjODRjZjdkMmNmOTU3MWUxMDlhZTc\\\" target=\\\"_blank\\\">Slack<\\\/a><\\\/li><li><a href=\\\"https:\\\/\\\/groups.google.com\\\/forum\\\/#!forum\\\/gilacms\\\" target=\\\"_blank\\\">Google Groups<\\\/a><\\\/li><\\\/ul>"}';
$wtext3 = '{"text":"<ul><li><a href=\\\\\"https:\\\/\\\/www.facebook.com\\\/gilacms\\\/\\\\\" target=\\\\\"_blank\\\\\">Facebook Page<\\\/a><\\\/li><li><a href=\\\\\"https:\\\/\\\/twitter.com\\\/GilaCms\\\\\" target=\\\\\"_blank\\\\\">Retweet<\\\/a> us!<\\\/li><li>Give a star on <a href=\\\\\"https:\\\/\\\/github.com\\\/GilaCMS\\\/gila\\\\\" target=\\\\\"_blank\\\\\">Github<\\\/a><\\\/li><li>Review on <a href=\\\\\"https:\\\/\\\/sourceforge.net\\\/projects\\\/gila-cms\\\/reviews\\\/new\\\\\" target=\\\\\"_blank\\\\\">SourceForge<\\\/a><\\\/li><\\\/ul>"}';

$link->query("INSERT INTO widget(id,widget,title,area,active,pos,data)
VALUES(1,'text','Start Blogging','dashboard',1,1,'".$wtext1."'),
(2,'text','Get Help','dashboard',1,2,'".$wtext2."'),
(3,'text','Support GilaCMS','dashboard',1,3,'".$wtext3."');");
