#!/bin/bash
cd libs
tar jxf php-5.5.33.tar.bz2
cd php-5.5.33/
./configure --prefix=/server/php5.5 --with-config-file-path=/server/php5.5/etc --with-config-file-scan-dir=/server/php5.5/etc/conf.d --with-gd --with-mcrypt=/usr/local/libmcrypt --with-libxml-dir=/usr/local/libxml2 --with-jpeg-dir=/usr/local/jpeg7 --with-png-dir=/usr/local/libpng --with-freetype-dir=/usr/local/freetype2 --with-mysql=mysqlnd  --with-mysqli=mysqlnd --with-pdo-mysql=mysqlnd  --enable-fpm --disable-phar --with-fpm-user=web --with-fpm-group=web --with-pcre-regex --with-zlib --with-bz2 --enable-calendar --with-curl --enable-dba  --enable-ftp  --enable-gd-native-ttf  --with-zlib-dir  --with-mhash --enable-mbstring  --enable-xml --disable-rpath  --enable-opcache  --enable-shmop --enable-sockets --enable-zip --enable-bcmath --disable-ipv6
make && make install
if [ -d "/server/php5.5/etc" ];then
        if [ ! -f "/server/php5.5/etc/php.ini-production" ];then
            cp php.ini-production /server/php5.5/etc
        fi
        if [ ! -f "/server/php5.5/etc/php.ini-development" ];then
            cp php.ini-development /server/php5.5/etc
        fi
        if [ ! -f "/server/php5.5/etc/php.ini" ];then
            cp ../etc/php.ini /server/php5.5/etc/php.ini
        fi
        if [ ! -f "/server/php5.5/etc/php.ini" ];then
            cp ../etc/php-cli.ini /server/php5.5/etc/php-cli.ini
        fi
        if [ ! -f "/server/php5.5/etc/php-fpm.conf" ];then
            cp ../etc/php-fpm.5.5.conf /server/php5.5/etc/php-fpm.conf
        fi
        if [ ! -f "/server/php5.5/fpm.sh" ];then
            cp ../etc/fpm.5.5.sh /server/php5.5/fpm.sh
        fi
        #==========================================================
        if [ -d "/server/php5.5/bin" ];then
            if [ ! -d "/server/php5.5/etc/conf.d" ];then
               mkdir /server/php5.5/etc/conf.d
            fi
        fi
        cd ext/pcntl/
        /server/php5.5/bin/phpize
        ./configure --with-php-config=/server/php5.5/bin/php-config
        make && make install
        if [ -f "/server/php5.5/lib/php/extensions/no-debug-non-zts-20090626/pcntl.so" ];then
            echo "[pcntl]">/server/php5.5/etc/conf.d/pcntl.ini
            echo "extension=pcntl.so">>/server/php5.5/etc/conf.d/pcntl.ini
        fi
        if [ -f "/server/php5.5/lib/php/extensions/no-debug-non-zts-20121212/opcache.so" ];then
            echo "[opcache]">/server/php5.5/etc/conf.d/opcache.ini
            echo "opcache.enable=1">>/server/php5.5/etc/conf.d/opcache.ini
            echo "opcache.memory_consumption=128">>/server/php5.5/etc/conf.d/opcache.ini
            echo "opcache.interned_strings_buffer=8">>/server/php5.5/etc/conf.d/opcache.ini
            echo "opcache.max_accelerated_files=4000">>/server/php5.5/etc/conf.d/opcache.ini
            echo "opcache.revalidate_freq=60">>/server/php5.5/etc/conf.d/opcache.ini
            echo "opcache.fast_shutdown=1">>/server/php5.5/etc/conf.d/opcache.ini
            echo "opcache.enable_cli=1">>/server/php5.5/etc/conf.d/opcache.ini
            echo "zend_extension=opcache.so">>/server/php5.5/etc/conf.d/opcache.ini
            /server/php5.5/bin/php -m
        fi
        if [ ! -f "/server/php5.5/bin/phpcli" ];then
            echo "#!/bin/bash">>/server/php5.5/bin/phpcli
            echo "/server/php5.5/bin/php -c /server/php5.5/etc/php-cli.ini $*">>/server/php5.5/bin/phpcli
            chmod +x /server/php5.5/bin/phpcli
        fi
        cd ../../
fi
u_exitst=`cat /etc/passwd|grep web|wc -l`
if [ ${u_exitst} -eq 0 ];then
   useradd web
fi
echo "done."