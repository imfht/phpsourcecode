#!/bin/bash
cd libs/php-5.5.33/ext/soap
/server/php5.5/bin/phpize --with-php-config=/server/php5.5/bin/php-config
 ./configure --with-php-config=/server/php5.5/bin/php-config
make && make install
if [ -f "/server/php5.5/lib/php/extensions/no-debug-non-zts-20121212//soap.so" ];then
    echo "[soap]">/server/php5.5/etc/conf.d/soap.ini
    echo "extension=soap.so">>/server/php5.5/etc/conf.d/soap.ini
    /server/php5.5/bin/php -m
fi
echo "done."