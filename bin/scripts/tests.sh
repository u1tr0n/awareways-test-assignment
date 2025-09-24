#!/bin/sh
APP_ENV=test bin/console doctrine:database:drop --force
APP_ENV=test bin/console doctrine:database:create
APP_ENV=test bin/console d:s:u -f
APP_ENV=test bin/console doctrine:fixtures:load --no-interaction
APP_ENV=test bin/phpunit --testdox
