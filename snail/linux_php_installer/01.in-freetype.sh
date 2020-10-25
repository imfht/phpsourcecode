#!/bin/bash
cd libs
echo "=====start install freetype-2.3.0 to /usr/local/freetype2====="
tar -zxvf freetype-2.4.0.tar.gz
cd freetype-2.4.0
./configure --prefix=/usr/local/freetype2
make && make install
cd ..
rm -fr freetype-2.4.0
cd ../
echo "done."
