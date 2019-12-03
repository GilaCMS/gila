<?php
global $db;

$db->query("ALTER TABLE `page` ADD COLUMN `blocks` TEXT;");
$db->query("ALTER TABLE `post` ADD COLUMN `blocks` TEXT;");
