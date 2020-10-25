#!/bin/bash
cd libs
tar -zxvf autoconf-2.13.tar.gz
cd autoconf-2.13
./configure --prefix=/usr/local/autoconf
make && make install
if [ -d "/usr/local/autoconf" ];then
    export PHP_AUTOCONF=/usr/local/autoconf/bin/autoconf
    export PHP_AUTOHEADER=/usr/local/autoconf/bin/autoheader
fi
cd ..
rm -rf autoconf-2.13
echo "done."
