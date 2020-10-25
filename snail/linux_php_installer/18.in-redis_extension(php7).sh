#!/bin/bash
cd libs
tar zxfv phpredis-php7.tar.gz
cd phpredis-php7
/server/php7/bin/phpize --with-php-config=/server/php7/bin/php-config
./configure --with-php-config=/server/php7/bin/php-config
make && make install
cd ..
rm -rf phpredis-php7
if [ -f "/server/php7/lib/php/extensions/no-debug-non-zts-20151012/redis.so" ];then
    echo "[redis]">/server/php7/etc/conf.d/redis.ini
    echo "extension=redis.so">>/server/php7/etc/conf.d/redis.ini
    /server/php7/bin/php -m
fi
echo "done."
