@echo off
title PHP-CGI
mode con cols=28 lines=1
:start
php-cgi.exe -b 127.0.0.1:9000 -c php.ini
goto start