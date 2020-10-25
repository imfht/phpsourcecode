#!/bin/bash
cd libs
mkdir /server
tar jxfv php-5.3.29.tar.bz2
cd php-5.3.29
#--with-snmp 如果启用这个，那么就要安装skip里面的拓展
./configure --prefix=/server/php5.3 --with-config-file-path=/server/php5.3/etc --with-config-file-scan-dir=/server/php5.3/etc/conf.d --with-gd=/usr/local/gd2 --with-mcrypt=/usr/local/libmcrypt --with-libxml-dir=/usr/local/libxml2 --with-jpeg-dir=/usr/local/jpeg6 --with-png-dir=/usr/local/libpng --with-freetype-dir=/usr/local/freetype2 --with-mysql=mysqlnd  --with-mysqli=mysqlnd --with-pdo-mysql=mysqlnd -enable-safe-mode --enable-fpm --disable-phar --with-fpm-user=web --with-fpm-group=web --with-pcre-regex --with-zlib --with-bz2 --enable-calendar --with-curl --enable-dba  --enable-ftp  --enable-gd-native-ttf  --with-zlib-dir  --with-mhash --enable-mbstring  --enable-xml --disable-rpath  --enable-shmop --enable-sockets --enable-zip --enable-bcmath --disable-ipv6
make && make install
if [ -d "/server/php5.3/etc" ];then
        if [ ! -f "/server/php5.3/etc/php.ini-production" ];then
            cp php.ini-production /server/php5.3/etc
        fi
        if [ ! -f "/server/php5.3/etc/php.ini-development" ];then
            cp php.ini-development /server/php5.3/etc
        fi
        if [ ! -f "/server/php5.3/etc/php.ini" ];then
            cp ../etc/php.ini /server/php5.3/etc/php.ini
        fi
        if [ ! -f "/server/php5.3/etc/php-cli.ini" ];then
            cp ../etc/php-cli.ini /server/php5.3/etc/php-cli.ini
        fi
        if [ ! -f "/server/php5.3/etc/php-fpm.conf" ];then
            cp ../etc/php-fpm.5.3.conf /server/php5.3/etc/php-fpm.conf
        fi
        if [ ! -f "/server/php5.3/fpm.sh" ];then
            cp ../etc/fpm.5.3.sh /server/php5.3/fpm.sh
        fi
        #==========================================================
        if [ -f "/usr/bin/pear" ];then
            mv /usr/bin/pear /usr/bin/pear.old
        fi
        if [ -f "/usr/bin/peardev" ];then
            mv /usr/bin/peardev /usr/bin/peardev.old
        fi
        if [ -f "/usr/bin/pecl" ];then
            mv /usr/bin/pecl /usr/bin/pecl.old
        fi
        if [ -f "/usr/bin/php" ];then
            mv /usr/bin/php /usr/bin/php.old
        fi
        if [ -f "/usr/bin/php-config" ];then
            mv /usr/bin/php-config /usr/bin/php-config.old
        fi
        if [ -f "/usr/bin/phpize" ];then
            mv /usr/bin/phpize /usr/bin/phpize.old
        fi
        if [ -d "/server/php5.3/bin" ];then
            ln -s /server/php5.3/bin/* /usr/bin/
            if [ ! -d "/server/php5.3/etc/conf.d" ];then
               mkdir /server/php5.3/etc/conf.d
            fi
        fi
        cd ext/pcntl/
        /server/php5.3/bin/phpize
        ./configure --with-php-config=/server/php5.3/bin/php-config
        make && make install
        if [ -f "/server/php5.3/lib/php/extensions/no-debug-non-zts-20090626/pcntl.so" ];then
            echo "[pcntl]">/server/php5.3/etc/conf.d/pcntl.ini
            echo "extension=pcntl.so">>/server/php5.3/etc/conf.d/pcntl.ini
            /server/php5.3/bin/php -m
        fi
        if [ ! -f "/server/php5.3/bin/phpcli" ];then
            echo "#!/bin/bash">>/server/php5.3/bin/phpcli
            echo "/server/php5.3/bin/php -c /server/php5.3/etc/php-cli.ini $*">/server/php5.3/bin/phpcli
            chmod +x /server/php5.3/bin/phpcli
        fi
        cd ../../
fi
u_exitst=`cat /etc/passwd|grep web|wc -l`
if [ ${u_exitst} -eq 0 ];then
   useradd web
fi
echo "done."
