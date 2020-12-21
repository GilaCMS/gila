<?php

TableSchema::update(include 'src/core/tables/post.php');

TableSchema::update(include 'src/core/tables/postcategory.php');

TableSchema::update(include 'src/core/tables/page.php');

TableSchema::update(include 'src/core/tables/user.php');

TableSchema::update(include 'src/core/tables/user_notification.php');

TableSchema::update(include 'src/core/tables/sessions.php');


if (!Config::get('set_utf8mb4')) {
  global $db;
  $db->query("ALTER DATABASE {$GLOBALS['config']['db']['name']} CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci");
  $tables = ['page','post','user','usermeta','userrole','postcategory','postmeta','user_notification','widget'];
  foreach ($tables as $table) {
    $db->query("ALTER TABLE `$table` CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci");
  }
  $db->query("ALTER TABLE `page` CHANGE blocks TEXT CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci");
  $db->query("ALTER TABLE `post` CHANGE post TEXT CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci");
  $db->query("ALTER TABLE `post` CHANGE `description` TEXT CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci");
  $db->query("ALTER TABLE `postcategory` CHANGE `description` TEXT CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci");
  $db->query("ALTER TABLE `user_notification` CHANGE `details` TEXT CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci");

  Config::get('set_utf8mb4', true);
}

Config::dir(LOG_PATH.'/stats');
Config::dir(LOG_PATH.'/cacheItem');
