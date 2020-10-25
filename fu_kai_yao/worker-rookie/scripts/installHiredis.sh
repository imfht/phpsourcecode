#!/bin/bash

#@author fukaiyao
#安装hiredis库，用于支持swoole异步redis客户端功能
set -x \
&& curl -s "https://codeload.github.com/redis/hiredis/tar.gz/v0.14.0" > hiredis-0.14.0.tar.gz \
&& tar -zxvf hiredis-0.14.0.tar.gz \
&& cd hiredis-0.14.0 \
&& sudo make -j$(nproc) && sudo make install && sudo ldconfig \
&& cd -