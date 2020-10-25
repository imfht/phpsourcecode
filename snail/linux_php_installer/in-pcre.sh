#!/bin/bash
cd libs
tar zxfv pcre-8.00.tar.gz
cd pcre-8.00
./configure --prefix=/usr/local/pcre8
make && make install
cd ..
rm -rf pcre-8.00
echo "done."
