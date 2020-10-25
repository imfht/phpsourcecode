#!/bin/bash
cd libs
echo "=====start install jpegsrc.v6b.tar.gz to /usr/local/jpeg6====="
mkdir /usr/local/jpeg6
mkdir /usr/local/jpeg6/bin
mkdir /usr/local/jpeg6/lib
mkdir /usr/local/jpeg6/include
mkdir /usr/local/jpeg6/man
mkdir /usr/local/jpeg6/man/man1
tar -zxvf jpegsrc.v6b.tar.gz
cd jpeg-6b
cp /usr/share/libtool/config/config.sub .
cp /usr/share/libtool/config/config.guess .
./configure --prefix=/usr/local/jpeg6/ --enable-shared --enable-static
make && make install
cd ../
rm -rf jpeg-6b
cd ..
echo "done."
