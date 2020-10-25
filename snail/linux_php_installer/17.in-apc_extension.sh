#!/bin/bash
cd libs
tar zxfv APC-3.1.9.tgz
cd APC-3.1.9
/server/php5.3/bin/phpize --with-php-config=/server/php5.3/bin/php-config
 ./configure
make && make install
cd ..
rm -rf cd APC-3.1.9
if [ -f "/server/php5.3/lib/php/extensions/no-debug-non-zts-20090626/apc.so" ];then
    echo "[apc]">/server/php5.3/etc/conf.d/apc.ini
    echo "extension=apc.so">>/server/php5.3/etc/conf.d/apc.ini
    echo "apc.mmap_file_mask=/tmp/apc.XXXXXX">>/server/php5.3/etc/conf.d/apc.ini
    echo "apc.shm_size=128M">>/server/php5.3/etc/conf.d/apc.ini
    echo "apc.ttl=3600">>/server/php5.3/etc/conf.d/apc.ini
    echo "apc.user_ttl=3600">>/server/php5.3/etc/conf.d/apc.ini
    /server/php5.3/bin/php -m
fi
echo "done."
