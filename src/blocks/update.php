<?php
global $db;

$db->query("ALTER TABLE `page` ADD COLUMN `blocks` TEXT;");
if (Config::config('page-blocks')===null) {
  $db->query("ALTER TABLE `post` ADD COLUMN `blocks` TEXT;");
}
