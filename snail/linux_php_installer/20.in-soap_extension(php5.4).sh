#!/bin/bash
cd libs/php-5.4.19/ext/soap
/server/php5.4/bin/phpize --with-php-config=/server/php5.4/bin/php-config
 ./configure --with-php-config=/server/php5.4/bin/php-config
make && make install
if [ -f "/server/php5.4/lib/php/extensions/no-debug-non-zts-20100525/soap.so" ];then
    echo "[soap]">/server/php5.4/etc/conf.d/soap.ini
    echo "extension=soap.so">>/server/php5.4/etc/conf.d/soap.ini
    /server/php5.4/bin/php -m
fi
echo "done."