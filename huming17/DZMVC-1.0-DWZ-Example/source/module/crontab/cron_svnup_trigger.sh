#!/bin/sh
/usr/bin/svn checkout --username xuhm --password 123456 svn://50.31.146.128:3309/branches/szbosshr /home/wwwroot/default
chmod -R 775 /home/wwwroot/default
chown -R www:www /home/wwwroot/default
