#!/bin/bash
cd libs
tar zxfv libxml2-2.7.2.tar.gz
cd libxml2-2.7.2
./configure --prefix=/usr/local/libxml2
make && make install
cd ..
rm -rf libxml2-2.7.2
cd ..
echo "done."
