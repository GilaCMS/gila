<?php

$postTable = new gTable('core/tables/post.php');
$postTable->update();

$userroleTable = new gTable('core/tables/userrole.php');
$userroleTable->update();

if(version_compare(package::version('core'), '1.8.0') < 0) {
    global $db;
    $db->query("ALTER TABLE `page` CHANGE COLUMN `page` content text;");
    $db->query("ALTER TABLE `postmeta` CHANGE COLUMN `vartype` `vartype` varchar(80);");
    $db->query("ALTER TABLE `postmeta` CHANGE COLUMN `value` `value` varchar(255);");
}

if(version_compare(package::version('core'),'1.9.0') < 0) {
  global $db;
  $db->query("ALTER TABLE `user` ADD COLUMN `active` tinyint(1) DEFAULT 1;");
  $db->query("ALTER TABLE `option` CHANGE COLUMN `value` `value` text;");
}

if(version_compare(package::version('core'),'1.10.9') < 0) {
  global $db;
  $db->query("ALTER TABLE `page` ADD COLUMN `template` varchar(30) DEFAULT NULL;");
  file_put_contents("lib/vue/vue-draggable.min.js",file_get_contents("src/core/lib/vue-draggable.min.js"));
}

if(version_compare(package::version('core'),'1.11.6') < 0) {
  global $db;
  $db->query("ALTER TABLE `postcategory` ADD COLUMN `slug` varchar(120) DEFAULT NULL;");
  $db->query("ALTER TABLE `postcategory` ADD COLUMN `description` varchar(200) DEFAULT NULL;");
}

if(version_compare(package::version('core'),'1.12.2') < 0) {
  global $db;
  $db->query("ALTER TABLE `post` ADD COLUMN `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;");
}

// always update them
file_put_contents("lib/gila.min.css",file_get_contents("src/core/lib/gila.min.css"));
file_put_contents("lib/gila.min.js",file_get_contents("src/core/lib/gila.min.js"));

