#!/bin/bash
# Скачивает свежие дампы баз studios с boom-ubuntu в docker/mysql/init и пересоздает локальную базу
set -e
cd "$(dirname "$0")"

for db in studios_boomroom_main studios_boomroom_site; do
    ssh boom-ubuntu "mysqldump --single-transaction --default-character-set=utf8 --databases $db | gzip" > docker/mysql/init/$db.sql.gz
done

docker compose down --volumes
docker compose up -d
