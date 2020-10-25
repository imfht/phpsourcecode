#!/bin/bash
cd libs
tar zxfv libevent-0.1.0.tgz
cd libevent-0.1.0/
/server/php5.4/bin/phpize --with-php-config=/server/php5.4/bin/php-config
./configure --with-php-config=/server/php5.4/bin/php-config
make && make install
cd ..
rm -rf cd libevent-0.1.0
if [ -f "/server/php5.4/lib/php/extensions/no-debug-non-zts-20100525/libevent.so" ];then
    echo "[libevent]">/server/php5.4/etc/conf.d/libevent.ini
    echo "extension=libevent.so">>/server/php5.4/etc/conf.d/libevent.ini
    /server/php5.4/bin/php -m
fi
echo "done."