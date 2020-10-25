#!/bin/sh
#########################################################################
# Add PHP Extension
# File Name: extension.sh
# Author: cccchu
# Email:  cccchu@163.com
# Version:
# Created Time: 2018/04/17
#########################################################################

#Add extension mongodb
curl -Lk https://pecl.php.net/get/mongodb-1.4.2.tgz | gunzip | tar x -C /home/extension && \
cd /home/extension/mongodb-1.4.2 && \
/usr/local/php/bin/phpize && \
./configure --with-php-config=/usr/local/php/bin/php-config && \
make && make install

#Add extension phpredis
curl -Lk https://pecl.php.net/get/redis-3.1.5.tgz | gunzip | tar x -C /home/extension && \
cd /home/extension/redis-3.1.5 && \
/usr/local/php/bin/phpize && \
./configure --with-php-config=/usr/local/php/bin/php-config && \
make && make install

#Add extension yaconf
#curl -Lk https://github.com/laruence/yaconf/archive/yaconf-1.0.7.tar.gz | gunzip | tar x -C /home/extension && \
#cd /home/extension/yaconf-1.0.7 && \
#/usr/local/php/bin/phpize && \
#./configure --with-php-config=/usr/local/php/bin/php-config && \
#make && make install