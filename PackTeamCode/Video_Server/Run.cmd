@echo off
title Launcher
echo Starting Redis Server
cd redis
start Redis.cmd
cd ..
echo Starting Web Process
cd nginx
start nginx.exe
cd ..\php
start CGI.cmd
echo Starting Encode Process
cd ..
start php\php.exe -c php\php.ini main.php
echo Starting Monitor
start php\php.exe -c php\php.ini monitor.php
pause