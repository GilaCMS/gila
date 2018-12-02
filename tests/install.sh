#!/bin/bash

# this script makes a new installation in localhost/gilatest
# you can change here the database credencials and accordingly on phpunit/InstallGila.php

sudo mysql -e "CREATE SCHEMA g_db" 2>/dev/null
sudo mysql -e "CREATE USER 'g_user'@'localhost' IDENTIFIED BY 'g_pass'" 2>/dev/null
sudo mysql -e "GRANT ALL PRIVILEGES ON g_db.* TO 'g_user'@'localhost'"
sudo mysql -e "FLUSH PRIVILEGES"
mv ../config.php back.config.php 2>/dev/null

./../vendor/phpunit/phpunit/phpunit phpunit/InstallGila.php