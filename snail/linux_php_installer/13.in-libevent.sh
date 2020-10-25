#!/bin/bash
cd libs
tar -zxvf libevent-1.4.13-stable.tar.gz
cd libevent-1.4.13-stable
 ./configure
make && make install
cd ..
rm -rf libevent-1.4.13-stable
echo "done."