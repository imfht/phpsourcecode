#!/bin/bash
# usage:
#	1.Copy Shell To SersyncPath
#       chmod +x checksersync.sh
#       2.Add Crontab
#       */5 * * * * [sersyncPath]/checksersync.sh

logfile='/var/log/checksersync.log';

function sersync_is_running(){
	threadnum=`ps aux|grep sersync2|grep -v grep -wc`;
	if [ "$threadnum" -eq '0' ];then
		echo '0';
	else
		echo '1';
	fi
	return;
}

function current_time(){
	if [ -z "$1" ];then
                format="%Y-%m-%d %H:%M:%S%Z";
        else
                format=$1;
        fi
        echo `date +"$format"`;
        return;
}

function logtofile(){
	echo $(current_time) $2>>$1;
}

function sersync_restart(){
	/usr/local/sersync/sersync2 -d -r -o /usr/local/sersync/confxml.xml >/dev/null 2>&1;
	sleep 3;
	threadnum=$(sersync_is_running);
	if [ $threadnum -eq '0' ]; then
		echo "0";
	else
		echo '1';
	fi
	return;
}

isrunning=$(sersync_is_running);

if [ "$isrunning" -eq '0' ];then
	logtofile $logfile "sersync service was died.";
	restart=$(sersync_restart);
	if [ $restart -eq '0' ];then
		logtofile $logfile "sersync service restart failed.";
	else
		logtofile $logfile "sersync service restart success.";
	fi
else
	logtofile $logfile "sersync service is running.";
fi
exit 0;