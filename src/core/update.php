<?php

TableSchema::update(include 'src/core/tables/post.php');

TableSchema::update(include 'src/core/tables/postcategory.php');

TableSchema::update(include 'src/core/tables/page.php');

TableSchema::update(include 'src/core/tables/user.php');

TableSchema::update(include 'src/core/tables/usergroup.php');

TableSchema::update(include 'src/core/tables/userrole.php');

TableSchema::update(include 'src/core/tables/user_notification.php');

TableSchema::update(include 'src/core/tables/sessions.php');

TableSchema::update(include 'src/core/tables/widget.php');

TableSchema::update(include 'src/core/tables/menu.php');

TableSchema::update(include 'src/core/tables/tableschema.php');

TableSchema::update(include 'src/core/tables/event_log.php');

TableSchema::update(include 'src/core/tables/redirect.php');

Config::dir(LOG_PATH.'/stats');
Config::dir(LOG_PATH.'/cacheItem');

global $db;
$db->query("UPDATE `page` SET language=? WHERE language IS NULL;", [Config::lang()]);
