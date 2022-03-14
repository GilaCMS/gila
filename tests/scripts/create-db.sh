#!/bin/bash

# this script makes a new demo database and user in localhost

sudo mysql -e "CREATE SCHEMA db_$1" 2>/dev/null
sudo mysql -e "CREATE USER 'user_$1'@'localhost' IDENTIFIED BY 'pass_$1'" 2>/dev/null
sudo mysql -e "GRANT ALL PRIVILEGES ON db_$1.* TO 'user_$1'@'localhost'"
sudo mysql -e "FLUSH PRIVILEGES"

