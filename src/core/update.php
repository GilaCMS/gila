<?php

Config::content('post', 'core/tables/post.php');
new TableSchema('post');

Config::content('page', 'core/tables/page.php');
new TableSchema('page');

Config::content('user', 'core/tables/user.php');
new TableSchema('user');

Config::content('userrole', 'core/tables/userrole.php');
new TableSchema('userrole');

if (version_compare(Package::version('core'), '1.8.0') < 0) {
  global $db;
  $db->query("ALTER TABLE `postmeta` CHANGE COLUMN `vartype` `vartype` varchar(80);");
  $db->query("ALTER TABLE `postmeta` CHANGE COLUMN `value` `value` varchar(255);");
}

if (version_compare(Package::version('core'), '1.9.0') < 0) {
  global $db;
  $db->query("ALTER TABLE `option` CHANGE COLUMN `value` `value` text;");
}

if (version_compare(Package::version('core'), '1.10.9') < 0) {
  global $db;
  file_put_contents("lib/vue/vue-draggable.min.js", file_get_contents("src/core/lib/vue-draggable.min.js"));
}

if (version_compare(Package::version('core'), '1.11.6') < 0) {
  global $db;
  $db->query("ALTER TABLE `postcategory` ADD COLUMN `slug` varchar(120) DEFAULT NULL;");
  $db->query("ALTER TABLE `postcategory` ADD COLUMN `description` varchar(200) DEFAULT NULL;");
}

if (version_compare(Package::version('core'), '1.12.2') < 0) {
  global $db;
  $db->query("ALTER TABLE `post` ADD COLUMN `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;");
}

if (version_compare(Package::version('core'), '1.13.0') < 0) {
  FileManager::copy('lib', 'assets/lib');
}

TableSchema::update(include 'src/core/tables/user_notification.php');

if (version_compare(Package::version('core'), '1.15.3') < 0) {
  $db->query("UPDATE userrole TABLE SET `level`=10 WHERE id=1;");
}

if (!Config::config('set_utf8mb4')) {
  $db->query("ALTER DATABASE {$GLOBALS['db']['name']} CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci");
  Config::config('set_utf8mb4', true);
}

Config::dir(LOG_PATH.'/stats');
Config::dir(LOG_PATH.'/cacheItem');
