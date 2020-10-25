#!/bin/bash
cd libs
tar zxf php-5.4.19.tar.gz
cd php-5.4.19
./configure --prefix=/server/php5.4 --with-config-file-path=/server/php5.4/etc --with-config-file-scan-dir=/server/php5.4/etc/conf.d --with-gd --with-mcrypt=/usr/local/libmcrypt --with-libxml-dir=/usr/local/libxml2 --with-jpeg-dir=/usr/local/jpeg7 --with-png-dir=/usr/local/libpng --with-freetype-dir=/usr/local/freetype2 --with-mysql=mysqlnd  --with-mysqli=mysqlnd --with-pdo-mysql=mysqlnd  --enable-fpm --disable-phar --with-fpm-user=web --with-fpm-group=web --with-pcre-regex --with-zlib --with-bz2 --enable-calendar --with-curl --enable-dba  --enable-ftp  --enable-gd-native-ttf  --with-zlib-dir  --with-mhash --enable-mbstring  --enable-xml --disable-rpath  --enable-shmop --enable-sockets --enable-zip --enable-bcmath --disable-ipv6
make && make install
if [ -d "/server/php5.4/etc" ];then
        if [ ! -f "/server/php5.4/etc/php.ini-production" ];then
            cp php.ini-production /server/php5.4/etc
        fi
        if [ ! -f "/server/php5.4/etc/php.ini-development" ];then
            cp php.ini-development /server/php5.4/etc
        fi
        if [ ! -f "/server/php5.4/etc/php.ini" ];then
            cp ../etc/php.ini /server/php5.4/etc/php.ini
        fi
        if [ ! -f "/server/php5.4/etc/php-cli.ini" ];then
            cp ../etc/php-cli.ini /server/php5.4/etc/php-cli.ini
        fi
        if [ ! -f "/server/php5.4/etc/php-fpm.conf" ];then
            cp ../etc/php-fpm.5.4.conf /server/php5.4/etc/php-fpm.conf
        fi
        if [ ! -f "/server/php5.4/fpm.sh" ];then
            cp ../etc/fpm.5.4.sh /server/php5.4/fpm.sh
        fi
        #==========================================================
        if [ -d "/server/php5.4/bin" ];then
            if [ ! -d "/server/php5.4/etc/conf.d" ];then
               mkdir /server/php5.4/etc/conf.d
            fi
        fi
        cd ext/pcntl/
        /server/php5.4/bin/phpize
        ./configure --with-php-config=/server/php5.4/bin/php-config
        make && make install
        if [ -f "/server/php5.4/lib/php/extensions/no-debug-non-zts-20090626/pcntl.so" ];then
            echo "[pcntl]">/server/php5.4/etc/conf.d/pcntl.ini
            echo "extension=pcntl.so">>/server/php5.4/etc/conf.d/pcntl.ini
            /server/php5.4/bin/php -m
        fi
        if [ ! -f "/server/php5.4/bin/phpcli" ];then
            echo "#!/bin/bash">>/server/php5.4/bin/phpcli
            echo "/server/php5.4/bin/php -c /server/php5.4/etc/php-cli.ini $*">>/server/php5.4/bin/phpcli
            chmod +x /server/php5.4/bin/phpcli
        fi
        cd ../../
fi
u_exitst=`cat /etc/passwd|grep web|wc -l`
if [ ${u_exitst} -eq 0 ];then
   useradd web
fi
echo "done."