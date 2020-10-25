#!/bin/bash
# wget http://downloads.zend.com/guard/5.5.0/ZendGuardLoader-php-5.3-linux-glibc23-i386.tar.gz
# wget http://downloads.zend.com/guard/5.5.0/ZendGuardLoader-php-5.3-linux-glibc23-x86_64.tar.gz
cd libs
cp ZendGuardLoader_32bit_php5.4.so /server/php5.4/lib/php/extensions/no-debug-non-zts-20100525/ZendGuardLoader.so
echo "[Zend.loader]">>/server/php5.4/etc/conf.d/ZendGuardLoader.ini
echo "zend_loader.enable=1">/server/php5.4/etc/conf.d/ZendGuardLoader.ini
echo "zend_extension=/server/php5.4/lib/php/extensions/no-debug-non-zts-20100525/ZendGuardLoader.so">>/server/php5.4/etc/conf.d/ZendGuardLoader.ini
/server/php5.4/bin/php -m |grep Zend
echo "done."