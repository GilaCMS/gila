<?php

if(version_compare($GLOBALS['version'],'1.7.6') < 0) {
    global $db;
    $db->query("ALTER TABLE `post` ADD KEY `user_id` (`user_id`);");
}

if(version_compare($GLOBALS['version'],'1.8.0') < 0) {
    global $db;
    $db->query("ALTER TABLE `page` CHANGE COLUMN `page` content text;");
    $db->query("ALTER TABLE `postmeta` CHANGE COLUMN `vartype` `vartype` varchar(80);");
    $db->query("ALTER TABLE `postmeta` CHANGE COLUMN `value` `value` varchar(255);");
}

if(version_compare($GLOBALS['version'],'1.9.0') < 0) {
  global $db;
  file_put_contents("lib/gila.min.css",file_get_contents("src/core/lib/gila.min.css"));
  file_put_contents("lib/gila.min.js",file_get_contents("src/core/lib/gila.min.js"));
  $db->query("ALTER TABLE `user` ADD COLUMN `active` tinyint(1) DEFAULT 1;");
  $db->query("ALTER TABLE `option` CHANGE COLUMN `value` `value` text;");
  $db->query("CREATE TABLE IF NOT EXISTS `userrole` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `userrole` varchar(80) DEFAULT NULL,
    KEY `id` (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}
