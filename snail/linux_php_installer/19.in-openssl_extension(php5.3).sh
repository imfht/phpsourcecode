#!/bin/bash
cd libs/php-5.3.29/ext/openssl
if [ -f "config0.m4" ];then
    mv config0.m4 config.m4
fi
/server/php5.3/bin/phpize --with-php-config=/server/php5.3/bin/php-config
 ./configure
make && make install
if [ -f "/server/php5.3/lib/php/extensions/no-debug-non-zts-20090626/openssl.so" ];then
    echo "[openssl]">/server/php5.3/etc/conf.d/openssl.ini
    echo "extension=openssl.so">>/server/php5.3/etc/conf.d/openssl.ini
    /server/php5.3/bin/php -m
fi
echo "done."