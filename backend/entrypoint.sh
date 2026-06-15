#!/bin/sh
php yii migrate/up --interactive=0
php-fpm -D && nginx -g 'daemon off;'