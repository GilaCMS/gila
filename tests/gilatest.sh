#!/bin/bash

# this script makes a new installation in localhost/gilatest and runs some basic unit and functional tests

# create the database
echo "Preperaring installation
"
sudo mysql -e "CREATE SCHEMA g_db" 2>/dev/null
sudo mysql -e "CREATE USER 'g_user'@'localhost' IDENTIFIED BY 'g_pass'" 2>/dev/null
sudo mysql -e "GRANT ALL PRIVILEGES ON g_db.* TO 'g_user'@'localhost'"
sudo mysql -e "FLUSH PRIVILEGES"
mv ../config.php back.config.php 2>/dev/null
echo "Done!"

# installation
echo "Installing GilaCMS
"
./../vendor/phpunit/phpunit/phpunit phpunit/InstallGila.php

# unit tests
echo "Run unit tests
"
./../vendor/phpunit/phpunit/phpunit phpunit/class-gila.php

# functional 
echo "Run jmeter tests
"
jmeter -n -t jmeter/basic.jmx -l basic.results.csv
cat basic.results.csv

# clean database
echo "
Cleaning up
============="
sudo mysql -e "DROP SCHEMA g_db"
rm -R ../tmp/* 2>/dev/null
rm -R ../log/* 2>/dev/null
mv back.config.php ../config.php 2>/dev/null
