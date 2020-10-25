#!/bin/bash
cd libs
echo "=====start install jpegsrc.v6b.tar.gz to /usr/local/jpeg6====="
tar -zxvf jpegsrc.v7.tar.gz
cd jpeg-7
./configure --prefix=/usr/local/jpeg7/ --enable-shared --enable-static
make && make install
cd ../
rm -rf jpeg-7
cd ..
echo "done."
