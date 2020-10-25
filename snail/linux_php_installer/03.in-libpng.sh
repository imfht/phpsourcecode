#!/bin/bash
cd libs
echo "=====start install libpng-1.2.29.tar.gz to /usr/local/libpng====="
tar zxvf libpng-1.2.29.tar.gz
cd libpng-1.2.29/
./configure --prefix=/usr/local/libpng
make && make install
cd ../
rm -rf libpng-1.2.29
cd ..
echo "done."
