#!/bin/bash

PHP_PATH=/alidata/server/php/bin/
AUTO_PATH=/alidata/www/default/auto/

echo '' > ${AUTO_PATH}log.txt

for file in `find ${AUTO_PATH} | grep \.php | grep -v pub_inc\.php | sort -r`
do
	${PHP_PATH}php -f $file >> ${AUTO_PATH}log.txt 
done
