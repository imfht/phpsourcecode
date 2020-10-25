#!/bin/bash
cd libs
tar zxvf libmcrypt-2.5.8.tar.gz 
cd libmcrypt-2.5.8
./configure --prefix=/usr/local/libmcrypt
make && make install
cd ../
rm -rf libmcrypt-2.5.8
cd ..

