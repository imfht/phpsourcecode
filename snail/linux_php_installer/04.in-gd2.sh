#!/bin/bash
cd libs
echo "=====start install gd-2.0.35.tar.gz to /usr/local/gd2====="
tar -zxvf gd-2.0.35.tar.gz
cd gd-2.0.35
mv gd_png.c gd_png.c.bak
cp ../etc/gd_png.c .
./configure --prefix=/usr/local/gd2 --with-png=/usr/local/libpng --with-freetype=/usr/local/freetype2 --with-jpeg=/usr/local/jpeg6
make && make install
cd ../
rm -rf gd-2.0.35
cd .. 
echo "done"
