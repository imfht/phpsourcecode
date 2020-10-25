#!/bin/bash

#@author fukaiyao
#安装swoole扩展
set -x \
&& curl -s "https://codeload.github.com/swoole/swoole-src/tar.gz/v4.4.0" > swoole-src-4.4.0.tar.gz \
&& tar -zxvf swoole-src-4.4.0.tar.gz \
&& cd swoole-src-4.4.0 \
&& phpize \
&& ./configure \
   --enable-openssl  \
   --enable-sockets \
   --enable-mysqlnd \
&& make clean \
&& make \
&& sudo make install \
&& echo 'extension=swoole.so' >> `php --ini|grep 'Loaded Configuration File' | awk '{print $4}'` \
&& service php-fpm reload \
&& cd - \


