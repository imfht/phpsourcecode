#!/bin/bash
cd libs
tar zxfv beecrypt-4.2.1.tar.gz
cd beecrypt-4.2.1
./configure --prefix=/usr --without-java --with-python=no --disable-openmp
make && make install
cd ..
rm -rf beecrypt-4.2.1
echo "done."
