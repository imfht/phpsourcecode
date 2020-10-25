#!/bin/bash
cd libs
tar zxfv libevent-0.1.0.tgz
cd libevent-0.1.0/
/server/php5.5/bin/phpize --with-php-config=/server/php5.5/bin/php-config
./configure --with-php-config=/server/php5.5/bin/php-config
make && make install
cd ..
rm -rf cd libevent-0.1.0
if [ -f "/server/php5.5/lib/php/extensions/no-debug-non-zts-20121212/libevent.so" ];then
    echo "[libevent]">/server/php5.5/etc/conf.d/libevent.ini
    echo "extension=libevent.so">>/server/php5.5/etc/conf.d/libevent.ini
    /server/php5.5/bin/php -m
fi
echo "done."