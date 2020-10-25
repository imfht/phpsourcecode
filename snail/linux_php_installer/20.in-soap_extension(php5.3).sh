#!/bin/bash
cd libs/php-5.3.29/ext/soap
if [ -f "config0.m4" ];then
    mv config0.m4 config.m4
fi
/server/php5.3/bin/phpize --with-php-config=/server/php5.3/bin/php-config
./configure
make && make install
if [ -f "/server/php5.3/lib/php/extensions/no-debug-non-zts-20090626/soap.so" ];then
    echo "[soap]">/server/php5.3/etc/conf.d/soap.ini
    echo "extension=soap.so">>/server/php5.3/etc/conf.d/soap.ini
    /server/php5.3/bin/php -m
fi
echo "done."