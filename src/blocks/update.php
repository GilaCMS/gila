<?php
global $db;

if (Config::config('page-blocks')===null) {
  $db->query("ALTER TABLE `post` ADD COLUMN `blocks` TEXT;");
  $db->query("ALTER TABLE `page` ADD COLUMN `blocks` TEXT;");
}
