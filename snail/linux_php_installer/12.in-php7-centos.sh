#!/bin/bash
yum install gcc-c++ gcc cmake cyrus-sasl-devel libmemcached php-memcached  build-essential bison   libreadline6 libreadline6-dev curl  libssl-dev libyaml-dev libxml2-dev libxslt-dev autoconf libc6-dev libcur* libxml2 libxml2-devel zlib1g zlib1g-dev  openssl openssl-devel bzip2 bzip2-devel curl curl-devel libjpeg libjpeg-devel  libpng libpng-devel  freetype-devel gmp-devel mysql-devel ncurses ncurses-devel unixODBC-devel  pspell-devel  libmcrypt libmcrypt-devel net-snmp net-snmp-devel mhash-devel libsqlite3-0 libsqlite3-dev sqlite3 
cd libs
tar jxf php-7.0.8.tar.bz2
cd php-7.0.8/
./configure --prefix=/server/php7  --with-config-file-path=/server/php7/etc  --with-config-file-scan-dir=/server/php7/etc/conf.d --with-mcrypt=/usr/include --with-mysqli=mysqlnd --with-pdo-mysql=mysqlnd --with-gd --with-iconv --with-zlib --enable-xml --enable-bcmath --enable-shmop --enable-sysvsem --enable-inline-optimization --enable-mbregex --enable-fpm --enable-mbstring --enable-ftp --enable-gd-native-ttf --with-openssl --enable-pcntl --enable-sockets --with-xmlrpc --enable-zip --enable-soap --without-pear --with-gettext --enable-session --with-curl --with-jpeg-dir --with-freetype-dir --enable-opcache
make && make install
if [ -d "/server/php7/etc" ];then
        if [ ! -f "/server/php7/etc/php.ini-production" ];then
            cp -R ../etc/php7/* /server/php7/etc/
        fi
        if [ ! -f "/server/php7/fpm.sh" ];then
            cp ../etc/fpm7.sh /server/php7/fpm.sh
        fi
        #==========================================================
        if [ -d "/server/php7/bin" ];then
            if [ ! -d "/server/php7/etc/conf.d" ];then
               mkdir /server/php7/etc/conf.d
            fi
        fi
        cd ../../
fi
u_exitst=`cat /etc/passwd|grep web|wc -l`
if [ ${u_exitst} -eq 0 ];then
   useradd web
fi
echo "done."