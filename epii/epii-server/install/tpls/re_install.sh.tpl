
echo stop nginx
{{nginx_cmd}} -s stop
pkill -9  php-fpm
pkill -9  php-fpm
pkill -9  php-fpm
pkill -9  php-fpm
pkill -9  php-cgi
pkill -9  php-cgi
pkill -9  php-cgi
pkill -9  php-cgi


{{php_cmd}} ./install/install.php

{{php_cmd}} ./default/start.php

