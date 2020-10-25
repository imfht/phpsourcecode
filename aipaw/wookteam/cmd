#!/usr/bin/env bash

COMPOSE="docker-compose"

if [ $# -gt 0 ];then
    if [[ "$1" == "artisan" ]]; then
        shift 1
        $COMPOSE run --rm -w /var/www php php artisan "$@"
    elif [[ "$1" == "php" ]]; then
        shift 1
        $COMPOSE run --rm -w /var/www php php "$@"
    elif [[ "$1" == "composer" ]]; then
        shift 1
        $COMPOSE run --rm -w /var/www php composer "$@"
    elif [[ "$1" == "supervisorctl" ]]; then
        shift 1
        $COMPOSE run --rm -w /var/www php supervisorctl "$@"
    elif [[ "$1" == "test" ]]; then
        shift 1
        $COMPOSE run --rm -w /var/www php ./vendor/bin/phpunit "$@"
    elif [[ "$1" == "npm" ]]; then
        shift 1
        $COMPOSE run --rm -w /var/www php npm "$@"
    elif [[ "$1" == "yarn" ]]; then
        shift 1
        $COMPOSE run --rm -w /var/www php yarn "$@"
    elif [[ "$1" == "mysql" ]]; then
        shift 1
        $COMPOSE run --rm -w / mariadb mysql "$@"
    elif [[ "$1" == "restart" ]]; then
        shift 1
        $COMPOSE stop
        $COMPOSE start
    else
        $COMPOSE "$@"
    fi
else
    $COMPOSE ps
fi
