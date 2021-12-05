<?php

$ctp = __DIR__.'/tables/';

TableSchema::update(include $ctp.'post.php');

TableSchema::update(include $ctp.'postcategory.php');

TableSchema::update(include $ctp.'page.php');

TableSchema::update(include $ctp.'user.php');

TableSchema::update(include $ctp.'usergroup.php');

TableSchema::update(include $ctp.'userrole.php');

TableSchema::update(include $ctp.'user_notification.php');

TableSchema::update(include $ctp.'sessions.php');

TableSchema::update(include $ctp.'widget.php');

TableSchema::update(include $ctp.'menu.php');

TableSchema::update(include $ctp.'tableschema.php');

TableSchema::update(include $ctp.'event_log.php');

TableSchema::update(include $ctp.'redirect.php');

Config::dir(LOG_PATH.'/stats');
Config::dir(LOG_PATH.'/cacheItem');

global $db;
$db->query("UPDATE `page` SET language=? WHERE language IS NULL;", [Config::lang()]);
