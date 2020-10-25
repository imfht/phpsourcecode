#!/bin/bash
# wget http://pecl.php.net/get/mongo-1.2.2.tgz //此php-driver包适用于php5.3;php5.4请选择1.2.12以上，1.2.12试用可以
# tar -zxvf mongodb-mongo-php-driver-1.2.9-112-gb9d5a08.tar.gz

# cd mongodb-1.2.2
# /usr/local/php/bin/phpize
# ./configure --enable-mongo=share --with-php-config=/usr/local/php/bin/php-config
# make && make install
cd libs
tar zxfv mongo-1.4.3.tgz
cd mongo-1.4.3
/server/php5.5/bin/phpize --with-php-config=/server/php5.5/bin/php-config
./configure --with-php-config=/server/php5.5/bin/php-config
make && make install
cd ..
rm -rf mongo-1.4.3
if [ -f "/server/php5.5/lib/php/extensions/no-debug-non-zts-20121212/mongo.so" ];then
    echo "[mongo]">/server/php5.5/etc/conf.d/mongo.ini
    echo "extension=mongo.so">>/server/php5.5/etc/conf.d/mongo.ini
    /server/php5.5/bin/php -m
fi
echo "done."
