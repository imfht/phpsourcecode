#!/bin/bash
cd libs
tar zxzf perl-5.8.9.tar.gz
cd perl-5.8.9
./Configure -des
make
make install
