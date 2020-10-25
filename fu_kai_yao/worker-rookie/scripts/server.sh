#!/bin/bash
#filename:cron和worker服务运行脚本
#author:fukaiyao
#date:2020-4-2

#. /etc/profile
basedir=`cd $(dirname $0); pwd`
cd $basedir

workerPidPath="../runtime/log/workerlog/workerServer.pid"
cronPidPath="../runtime/log/workerlog/crond.pid"

#同时停止cron和worker
stop() {
    if [ -f "$cronPidPath" ]; then
        cronPid=`cat $cronPidPath`
        echo "stop crond server, pid="$cronPid"..."

        check_crond_exist
        pidIsExits=$?
        if [ $pidIsExits -eq 1 ]; then
            kill $cronPid
        else
            echo "crond server not exist."
            rm -f $cronPidPath
        fi
     fi

     if [ -f "$workerPidPath" ]; then
         workerPid=`cat $workerPidPath`
         echo "stop worker server, pid="$workerPid"..."

         check_worker_exist
         pidIsExits=$?
         if [ $pidIsExits -eq 1 ]; then
             kill $workerPid
         else
             echo "worker server not exist."
             rm -f $workerPidPath
         fi
     fi

	isFalse=1
	cronIsFalse=1
	workerIsFalse=1
    try=0
    while test $try -lt 60; do
        if [ ! -f "$cronPidPath" ] && [ ! -f "$workerPidPath" ]; then
			try=''
			isFalse=0
            break
		else
			if [ ! -f "$cronPidPath" ]; then
				cronIsFalse=0
			else
			    kill $cronPid > /dev/null 2>&1
            fi

			if [ ! -f "$workerPidPath" ]; then
				workerIsFalse=0
			else
			    kill $workerPid > /dev/null 2>&1
            fi

        fi
        echo -n
        try=`expr $try + 1`
        sleep 1
    done

	if [ $isFalse -eq 1 ];then
		if [ $cronIsFalse -eq 1 ];then
			echo "stop cron timeout failed."
		else
			echo "stop crond ok."
		fi
		if [ $workerIsFalse -eq 1 ];then
	        echo "stop worker timeout failed."
		else
			echo "stop workerServer ok."
		fi
	else
	    echo "stop cron && workerServer ok."
	fi

	return $isFalse
}

#强制退出
forceStop() {
    nohup bash ./crond.sh stop -t 900 > /dev/null 2>&1 &
    nohup bash ./workerServer.sh stop -t 900 > /dev/null 2>&1 &
    echo "stop cron && workerServer ok3."
	return 0
}

#同时启动cron和worker
start() {
    bash ./crond.sh start
    bash ./workerServer.sh start
	return 0
}

#重启cron和worker服务
restart() {
    hasT=1
    for i in `seq $#`; do
        param=`eval echo '$'$i`
        case "$param" in
        -t)
            hasT=0
            ;;
        esac
    done

    if [ $hasT -eq 0 ];then
        nohup bash ./crond.sh restart -t > /dev/null 2>&1 &
        nohup bash ./workerServer.sh restart -t > /dev/null 2>&1 &
        echo "restart cron && workerServer ok."
        return 0
    else
        if stop && start;then
            echo "restart cron && workerServer ok."
            return 0
        else
            echo "restart cron && workerServer failed."
            return 1
        fi
    fi
}

#检测workerServer进程是否存在
check_worker_exist() {
    if [ ! -f "$workerPidPath" ]; then
        return 0
    fi

    pid=`cat $workerPidPath`
    pids=`ps aux | grep workerServer.php | grep -v grep | awk '{print $2}'`
    pidIsExits=0;
    for i in ${pids}
        do
            if [ $i -eq $pid ]; then
                pidIsExits=1
                break
            fi

        done
    return  $pidIsExits
}

#检测crond进程是否存在
check_crond_exist() {
    if [ ! -f "$cronPidPath" ]; then
        return 0
    fi

    pid=`cat $cronPidPath`
    pids=`ps aux | grep crond.php | grep -v grep | awk '{print $2}'`
    pidIsExits=0;
    for i in ${pids}
        do
            if [ $i -eq $pid ]; then
                pidIsExits=1
                break
            fi

        done
    return  $pidIsExits
}


case "$1" in
start)
    start
    ;;
stop)
    hasT=1
    for i in `seq $#`; do
        param=`eval echo '$'$i`
        case "$param" in
        -t)
            hasT=0
            ;;
        esac
    done

    if [ $hasT -eq 0 ];then
       forceStop
    else
       stop
    fi
    ;;
restart)
    restart `echo $*|xargs -n 1|grep -v $1`
    ;;
*)
    echo "Usage: server.sh {start|stop|restart|help}"
    exit 1
esac
