<?php

if(version_compare($GLOBALS['version'],'1.7.5') < 0) {
    file_put_contents("lib/gila.min1.css",file_get_contents("src/core/lib/gila.min.css"));
}

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
