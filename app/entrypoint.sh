#!/bin/bash

sleep 25

cd /var/www/app

rm -rf var/cache/*
rm -rf migrations/Version*
rm -rf var/log/messenger*

php bin/console doctrine:database:create --if-not-exists
php bin/console make:migration --no-interaction
php bin/console doctrine:migrations:migrate --no-interaction

php bin/console --env=test doctrine:database:create --if-not-exists
php bin/console --env=test doctrine:schema:create

php bin/console --env=test doctrine:fixtures:load --no-interaction

# Start Supervisor service
/usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf &

php-fpm