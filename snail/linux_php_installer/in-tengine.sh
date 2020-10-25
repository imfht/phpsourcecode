#!/bin/bash
cd libs
#tar zxfv pcre-8.00.tar.gz -C /tmp/
#chmod -R 777 /tmp/pcre-8.00

tar zxfv ngx_devel_kit-0.2.17rc2.tar.gz -C /tmp/
chmod -R 777 /tmp/ngx_devel_kit-0.2.17rc2

if [ ! -d "/usr/local/include/luajit-2.0" ];then
        tar zxvf LuaJIT-2.0.4.tar.gz
        cd LuaJIT-2.0.4
        make
        make install
        export LUAJIT_LIB=/usr/local/lib
        export LUAJIT_INC=/usr/local/include/luajit-2.0
        cd ..
fi

tar zxfv tengine-2.1.2.tar.gz
chmod -R 777 tengine-2.1.2
cd tengine-2.1.2
# --with-pcre=/tmp/pcre-8.00
./configure --prefix=/server/tengine  --with-http_sub_module --with-http_gzip_static_module  --with-ld-opt="-Wl,-rpath,/usr/local/lib"  --with-http_lua_module --with-luajit-inc=/usr/local/include/luajit-2.0 --with-luajit-lib=/usr/local/lib --add-module=/tmp/ngx_devel_kit-0.2.17rc2
make && make install
cd ..

if [ ! -d "/server/tengine/logs" ];then
        mkdir /server/tengine/logs
fi
if [ ! -d "/server/tengine/logs/waf" ];then
        mkdir /server/tengine/logs/waf
fi

rm -rf tengine-2.1.2
rm -rf /tmp/pcre-8.00
rm -rf LuaJIT-2.0.4
rm -rf /tmp/ngx_devel_kit-0.2.17rc2

if [ -d "/server/tengine/conf" ];then
        if [ ! -d "/server/tengine/conf/vhost" ];then
            cp -R etc/ngx_lua /server/tengine/conf/
            cp -R etc/vhost /server/tengine/conf/
	    cp -R etc/ngx_php /server/tengine/conf/
	    cp etc/upstream_backend.conf /server/tengine/conf/
	    cp etc/status.conf /server/tengine/conf/
            cp etc/common_access.conf /server/tengine/conf/
            cp etc/common_file.conf /server/tengine/conf/
            cp etc/nginx.conf /server/tengine/conf/
            if [ -f "/etc/redhat-release" ];then
                cp etc/nginx_centos.sh /server/tengine/nginx.sh
            else
                cp etc/nginx_ubuntu.sh /server/tengine/nginx.sh
            fi
            cp etc/vhost.php /server/tengine/
            mkdir /server/tengine/tmp
        fi
fi
u_exitst=`cat /etc/passwd|grep web|wc -l`
if [ ${u_exitst} -eq 0 ];then
   useradd web
fi

chown -R web.web /server/tengine/logs
chown -R web.web /server/tengine/logs/waf

/server/tengine/sbin/nginx -v
echo "done."