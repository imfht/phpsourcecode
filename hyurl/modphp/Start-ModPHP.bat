@echo off
:top
cls
title %~f0
echo 1. Start Console
echo 2. Start HTTP Server
echo 3. Start Socket Server
echo 4. Start Socket Server in Multi-Threads
set/p option="Choose an option to start: "
if %option% == 4 goto SocketServerThread
if %option% == 3 goto SocketServer
if %option% == 2 goto HttpServer
if %option% == 1 cls & php mod.php & pause & goto top
if %option% == exit exit
goto top

:SocketServerThread
set/p port="Set an available port for listening: "
set/p threads="How many threads need to start: "
php socket-server-thread.php %port% %threads% & pause & goto top

:SocketServer
set/p port="Set an available port for listening: "
php socket-server.php %port% & pause & goto top

:HttpServer
set/p port="Set an available port for listening: "
php -S 0.0.0.0:%port% index.php & pause & goto top