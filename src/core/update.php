<?php

TableSchema::update(include 'src/core/tables/post.php');

TableSchema::update(include 'src/core/tables/postcategory.php');

TableSchema::update(include 'src/core/tables/page.php');

TableSchema::update(include 'src/core/tables/user.php');

TableSchema::update(include 'src/core/tables/user_notification.php');

TableSchema::update(include 'src/core/tables/sessions.php');

Config::dir(LOG_PATH.'/stats');
Config::dir(LOG_PATH.'/cacheItem');
