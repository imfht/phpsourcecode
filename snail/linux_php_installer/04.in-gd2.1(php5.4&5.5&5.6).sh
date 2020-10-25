#!/bin/bash
cd libs
echo "=====start install gd-2.0.35.tar.gz to /usr/local/gd2====="
tar -zxvf gd-2.1.0.tar.gz
cd libgd-gd-libgd-9f0a7e7f4f0f
cmake .
make && make install
cd ../
rm -rf libgd-gd-libgd-9f0a7e7f4f0f
cd .. 
echo "done"
