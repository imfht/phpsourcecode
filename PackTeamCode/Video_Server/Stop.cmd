@echo off
echo Shutdown PHP-CGI
taskkill /f /im php-cgi.exe
echo Safe Shutdown Nginx
cd nginx
nginx -s stop
taskkill /f /im nginx.exe
cd ..
echo.
echo.
echo.
echo.
echo.
echo Please enter Ctrl+C in "Redis Server" Window to save data and quit redis.
echo Please Manual Shutdown Main Thread and Worker Thread.
pause