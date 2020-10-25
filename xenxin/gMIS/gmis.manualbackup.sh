#!/bin/sh 
# create backup anytime from shell commands
# since 20110414
# update on Wed Jul 13 17:48:50 UTC 2011

me=`whoami`;
echo $me;

#if [ "$me" != "root" ]; then
#    echo "This program needs root priviledges to run.";
#    exit;
#fi

curdate="`date +%Y%m%d-%H-%M`";
echo "current time:"$curdate ;
#echo "test point.";
#exit ;

tmpdir="/var/tmp";

# rm old files
rm -f $tmpdir/gtbl.*

# files
dir="/www/webroot/pages/gmis";
cd $dir
tar czfh $tmpdir/gmis.$curdate.tar.gz ./* 

sz $tmpdir/gmis.$curdate.tar.gz

# data
mysqldump --opt -ugmisUSER -pgmisPWD gmisdb --default-character-set=utf8 > $tmpdir/gmis-db.$curdate.sql

tar czfh $tmpdir/gmis-db.$curdate.tar.gz $tmpdir/gmis-db.$curdate.sql

sz $tmpdir/gmis-db.$curdate.tar.gz

echo "$curdate data backup succ." >> $tmpdir/manualbackup.log

echo "databackup succ." ;

