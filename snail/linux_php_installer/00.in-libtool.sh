#!/bin/bash
cd libs
tar zxvf libtool-2.2.6a.tar.gz
cd libtool-2.2.6
./configure
make && make install
cd ..
rm -rf libtool-2.2.6
cd ..

