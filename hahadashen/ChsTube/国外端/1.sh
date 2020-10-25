#!/bin/sh
getid=`curl -d "sid=1&serverip=23.91.96.157&passkey=testserver&time=1" http://chstube.com/soure/api.php` > /dev/null
echo [WebServer-INFO]:The Video Query ID:$getid
geturl=`curl -d "sid=1&serverip=23.91.96.157&passkey=testserver&time=2&id=$getid" http://chstube.com/soure/api.php` > /dev/null
echo [WebServer-INFO]:The Video URL:$geturl
echo [Youtube-Dl-INFO]:Waiting......
youtube-dl -F $geturl > out.txt
line=`sed -n '$=' out.txt`
for ((i=1;i<=line;i++))
do
if ((i>=7))
then
rline=`sed -n "$i p" out.txt`
vid=$getid
id=`echo ${rline:0:13}`
ext=`echo ${rline:13:11}`
res=`echo ${rline:24:11}`
note=`echo ${rline:35:100}`
echo [Youtube-Dl-OUT]VideoID=$vid Code=$id Ext=$ext Res=$res Note=$note
end=`curl -d "sid=1&serverip=23.91.96.157&passkey=testserver&time=3&do=0&vid=$vid&code=$id&ext=$ext&res=$res&note=$note" http://chstube.com/soure/api.php` > /dev/null
echo [WebServer-INFO]:Mysql Back $end
continue
fi
done
last=`curl -d "sid=1&serverip=23.91.96.157&passkey=testserver&time=3&do=1&vid=$vid" http://chstube.com/soure/api.php`
echo [WebServer-INFO]:WebServer Back Code= $last
rm -rf out.txt
