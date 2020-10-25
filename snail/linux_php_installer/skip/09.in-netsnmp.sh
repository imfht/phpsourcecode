#!/bin/bash
cd libs
tar zxfv net-snmp-5.4.1.tar.gz
cd net-snmp-5.4.1
./configure
make && make install
cd ..
rm -rf net-snmp-5.4.1
echo "done."
