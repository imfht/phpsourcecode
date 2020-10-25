#!/bin/bash
cd libs
tar zxfv bzip2-1.0.5.tar.gz
cd bzip2-1.0.5
make && make install
cd ..
rm -rf bzip2-1.0.5
echo "done"
