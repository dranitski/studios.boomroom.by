#!/bin/bash
# Creates the app user with the credentials from the site's secrets-local.php (mounted read-only at /site-config.php)
set -e

get() { sed -n "s/.*define('$1', '\(.*\)');.*/\1/p" /site-config.php; }
user=$(get _DB_USER)
pass=$(get _DB_PASS)

mysql -uroot -p"$MYSQL_ROOT_PASSWORD" <<SQL
CREATE USER IF NOT EXISTS '${user}'@'%' IDENTIFIED BY '${pass//\'/\'\'}';
CREATE USER IF NOT EXISTS '${user}'@'localhost' IDENTIFIED BY '${pass//\'/\'\'}';
GRANT ALL ON studios_boomroom_site.* TO '${user}'@'%', '${user}'@'localhost';
GRANT ALL ON studios_boomroom_main.* TO '${user}'@'%', '${user}'@'localhost';
FLUSH PRIVILEGES;
SQL
