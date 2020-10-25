#!/bin/sh
getid=`curl -d "sid=1&serverip=23.91.96.157&passkey=testserver&time=4&do=1" http://chstube.com/soure/api.php`
getcode=`curl -d "sid=1&serverip=23.91.96.157&passkey=testserver&time=4&do=2&id=$getid" http://chstube.com/soure/api.php`
geturl=`curl -d "sid=1&serverip=23.91.96.157&passkey=testserver&time=4&do=3&id=$getid" http://chstube.com/soure/api.php`
getext=`curl -d "sid=1&serverip=23.91.96.157&passkey=testserver&time=4&do=4&id=$getid" http://chstube.com/soure/api.php`
ntime=`date +%s`
cd /data/web/vcache
echo [Youtube-DL] Start Download URL=$geturl Code=$getcode
youtube-dl -f $getcode -o$ntime.$getext $geturl
filename=$ntime.$getext
mv $filename ../video
back=`curl -d "sid=1&serverip=23.91.96.157&passkey=testserver&time=4&do=5&id=$getid&name=$filename" http://chstube.com/soure/api.php`
echo "back"