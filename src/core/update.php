<?php

Gila::content('post','core/tables/post.php');
$postTable = new gTable('post');
$postTable->update();

Gila::content('page','core/tables/page.php');
$pageTable = new gTable('page');
$pageTable->update();

Gila::content('userrole','core/tables/userrole.php');
$userroleTable = new gTable('userrole');
$userroleTable->update();

if(version_compare(Package::version('core'), '1.8.0') < 0) {
    global $db;
    $db->query("ALTER TABLE `postmeta` CHANGE COLUMN `vartype` `vartype` varchar(80);");
    $db->query("ALTER TABLE `postmeta` CHANGE COLUMN `value` `value` varchar(255);");
}

if(version_compare(Package::version('core'),'1.9.0') < 0) {
  global $db;
  $db->query("ALTER TABLE `user` ADD COLUMN `active` tinyint(1) DEFAULT 1;");
  $db->query("ALTER TABLE `option` CHANGE COLUMN `value` `value` text;");
}

if(version_compare(Package::version('core'),'1.10.9') < 0) {
  global $db;
  file_put_contents("lib/vue/vue-draggable.min.js",file_get_contents("src/core/lib/vue-draggable.min.js"));
}

if(version_compare(Package::version('core'),'1.11.6') < 0) {
  global $db;
  $db->query("ALTER TABLE `postcategory` ADD COLUMN `slug` varchar(120) DEFAULT NULL;");
  $db->query("ALTER TABLE `postcategory` ADD COLUMN `description` varchar(200) DEFAULT NULL;");
}

if(version_compare(Package::version('core'),'1.12.2') < 0) {
  global $db;
  $db->query("ALTER TABLE `post` ADD COLUMN `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;");
}

if(version_compare(Package::version('core'),'1.13.0') < 0) {
  FileManager::copy('lib','assets/lib');
}
