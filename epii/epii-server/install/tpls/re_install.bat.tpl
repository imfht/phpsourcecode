

@echo off
echo Stopping nginx...
taskkill /F /IM nginx.exe > nul
echo Stopping PHP FastCGI...
taskkill /F /IM php-cgi.exe > nul

@echo off

{{php_cmd}} ./install/install.php

{{php_cmd}} ./default/start.php

