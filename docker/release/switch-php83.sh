#!/bin/bash
# Switches the boom-studios stack on boom-ubuntu from PHP-FPM 5.6 to 8.3. Run as root from the repo root:
#   bash docker/release/switch-php83.sh
# Rollback: restore ~/compose/compose.yaml.bak-php56 and run "docker compose up -d --build php" again.
set -euo pipefail

HOST=boom-ubuntu
C=/home/boom-studios/compose

rsync -a --delete docker/release/php/ $HOST:$C/php83/
ssh $HOST "cp -n $C/compose.yaml $C/compose.yaml.bak-php56"
scp docker/release/compose.yaml $HOST:$C/compose.yaml
ssh $HOST "chown -R boom-studios:boom-studios $C && cd /tmp && sudo -u boom-studios -s /bin/bash -c 'export XDG_RUNTIME_DIR=/run/user/1004 DOCKER_HOST=unix:///run/user/1004/docker.sock; cd $C && docker compose up -d --build php && docker exec php php -v | head -1'"
