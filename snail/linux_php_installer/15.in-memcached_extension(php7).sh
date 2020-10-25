#!/bin/bash
#libmemcached10 有可能是libmemcached11 ubuntu版本不一样这个也不一样，可以tab补全看看是多少
apt-get install libmemcached-dev libmemcached10
cd libs
tar zxfv php-memcached.tar.gz
cd php-memcached
/server/php7/bin/phpize --with-php-config=/server/php7/bin/php-config
./configure --with-php-config=/server/php7/bin/php-config  --disable-memcached-sasl
make && make install
cd ..
rm -rf php-memcached
if [ -f "/server/php7/lib/php/extensions/no-debug-non-zts-20151012/memcached.so" ];then
    echo "[memcached]">/server/php7/etc/conf.d/memcached.ini
    echo "extension=memcached.so">>/server/php7/etc/conf.d/memcached.ini
    /server/php7/bin/php -m
fi
echo "done."
