# Image Force

Image Force is a duplicate image storing and availability ensuring system 
designed for small personal websites.

Image Force 是一个专为小型个人站点设计的图片多点存储及可用性保障系统。

## Features
 
Image Force is designed for small websites or projects. 
When a image is uploaded to this system, it will be add to a queue to apply sync duplication.
When a uploaded image is visited, it will be add to a queue to apply sync availability check.

## Requirements

- PHP 7+

## Install

```
git clone https://github.com/takashiki/image-force
composer install --no-dev -o
# or `composer create-project takashiki/image-force:@dev`

cd image-force

cp .env.example .env
vi .env # adjust your settings

php artisan migrate
```

add supervisor config:

```
[program:image-force-worker]
process_name=%(program_name)s_%(process_num)02d
command=/path/to/php /path/to/image-force/artisan queue:work 
--sleep=1 --tries=3 --daemon --queue=duplicate,check,default 
autostart=true
autorestart=true
user=root
environment=HOME='/root'
redirect_stderr=true
stdout_logfile=/path/to/image-force/worker.log
```