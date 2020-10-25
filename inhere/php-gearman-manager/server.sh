#!/bin/bash
SVR=127.0.0.1:5888
echo "http server listen on $SVR"
php -S $SVR -t web > ./examples/logs/server.log
