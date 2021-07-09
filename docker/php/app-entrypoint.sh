#!/usr/bin/env sh
set -e

APP_DIR="${APP_DIR:-/app}";
STARTUP_START_SUPERVISORD="${STARTUP_START_SUPERVISORD:-false}"
STARTUP_START_CONSUMERS="${STARTUP_START_CONSUMERS:-false}"
STARTUP_START_CRON="${STARTUP_START_CRON:-false}"
CRON_TIME="${CRON_TIME:-* * * * *}"

if [ "${STARTUP_START_SUPERVISORD}" = "true" ]; then
    echo "[INFO] Starting supervisord...";
    /usr/bin/supervisord -c /etc/supervisord.conf;
    echo "[INFO] supervisord successfully started";
fi;


if [ "${STARTUP_START_CRON}" = "true" ]; then
    echo "[INFO] Appending schedule to cron...";
    env | sed -r 's/^([^=]*)=(.*)/export \1=\"\2\"/' > /tmp/.env;
    echo "${CRON_TIME} . /tmp/.env; cd ${APP_DIR} && php artisan schedule:run >> /dev/null 2>&1" | crontab -;
    echo "[INFO] Appending schedule to cron completed";
    echo "[INFO] Starting cron process...";
    /usr/bin/supervisorctl start cron;
    echo "[INFO] Cron process successfully started";
fi;

if [ "${STARTUP_START_CONSUMERS}" = "true" ]; then
    echo "[INFO] Starting consumers processes...";
    /usr/bin/supervisorctl start queue-workers:*;
    echo "[INFO] consumers processes successfully started";
fi;

cd "${APP_DIR}" && php artisan optimize
touch "/${APP_DIR}/storage/preload.php"

exec "$@";
