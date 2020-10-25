#!/bin/bash
cd libs/php-5.4.19/ext/openssl
if [ -f "config0.m4" ];then
    mv config0.m4 config.m4
fi
/server/php5.4/bin/phpize --with-php-config=/server/php5.4/bin/php-config
 ./configure --with-php-config=/server/php5.4/bin/php-config
make && make install
if [ -f "/server/php5.4/lib/php/extensions/no-debug-non-zts-20100525/openssl.so" ];then
    echo "[openssl]">/server/php5.4/etc/conf.d/openssl.ini
    echo "extension=openssl.so">>/server/php5.4/etc/conf.d/openssl.ini
    /server/php5.4/bin/php -m
fi
echo "done."