<?php
$link->query('CREATE TABLE IF NOT EXISTS `post` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(80) CHARACTER SET latin1 DEFAULT NULL,
  `slug` varchar(80) CHARACTER SET latin1 DEFAULT NULL,
  `post` text CHARACTER SET latin1,
  `publish` int(1) DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `publish` (`publish`),
  KEY `user_id` (`user_id`),
  FULLTEXT KEY `title` (`title`,`post`)
)');

$link->query('CREATE TABLE IF NOT EXISTS `postmeta` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) DEFAULT NULL,
  `vartype` varchar(25) DEFAULT NULL,
  `value` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

$link->query('CREATE TABLE IF NOT EXISTS `postmeta` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) DEFAULT NULL,
  `vartype` varchar(25) DEFAULT NULL,
  `value` varchar(80) DEFAULT NULL,
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
  `slug` varchar(80) DEFAULT NULL,
  `page` text,
  `publish` int(1) DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

$link->query('CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(80) DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL,
  `pass` varchar(120) DEFAULT NULL,
  `reset_code` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

$link->query('CREATE TABLE IF NOT EXISTS `usermeta` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `vartype` varchar(80) DEFAULT NULL,
  `value` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

$link->query('CREATE TABLE IF NOT EXISTS `widget` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `widget` varchar(80) DEFAULT NULL,
  `title` varchar(80) DEFAULT NULL,
  `area` varchar(80) DEFAULT NULL,
  `active` int(1) DEFAULT 1,
  `pos` int(2) DEFAULT NULL,
  `data` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

$link->query('CREATE TABLE IF NOT EXISTS `option` (
  `option` varchar(80) NOT NULL,
  `value` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`option`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

$_user=$_POST['adm_user'];
$_email=$_POST['adm_email'];
$_pass=password_hash($_POST['adm_pass'], PASSWORD_BCRYPT);

$link->query("INSERT INTO user VALUES(1,'$_user','$_email','$_pass','');");
$link->query("INSERT INTO usermeta VALUES(1,1,'privilege','admin');");
$link->query("INSERT INTO post VALUES(1,1,'Hello World','hello_world','This is the first post',1,CURRENT_TIMESTAMP);");
$link->query("INSERT INTO page VALUES(1,'About','about','This is a page to describe your website',1,CURRENT_TIMESTAMP);");
$data = '{"menu":"[\\\n\\\t{\\\"title\\\":\\\"Home\\\",\\\"url\\\":\\\"\\\"},\\\n\\\t{\\\"title\\\":\\\"Page\\\",\\\"url\\\":\\\"page\\\"}\\\n]"}';
$link->query("INSERT INTO widget VALUES(1,'menu','head',1,1,'$data');");
