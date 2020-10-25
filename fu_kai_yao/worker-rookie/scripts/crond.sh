#!/bin/bash
#filename:cron服务运行脚本
#author:fukaiyao
#date:2020-4-2

#. /etc/profile
basedir=`cd $(dirname $0); pwd`
cd $basedir
logPath="../runtime/log/workerlog/"
pidPath="${logPath}crond.pid"
logFileName="crond.log"
myStopPidPath="${logPath}stopcrond.pid"

#需要分割日志的cronolog地址
cronolog=""

phpbin=`cat ../config/config.php |grep phpbin|awk -F "[\"']" '{print $4}'`

if [ ! -f "$phpbin" ]; then
   echo 'php命令路径未找到'
   exit 1
fi

if [ ! -d "$logPath" ]; then
   mkdir -p $logPath
fi

stop() {
    check_mystop_exist
    stopIsExist=$?
    if [ $stopIsExist -eq 1 ];then
        echo "crond server is being stopped..."
        exit 1
    else
        echo $$ > $myStopPidPath
    fi

	isFalse=0
    if [ -f "$pidPath" ]; then
        pid=`cat $pidPath`
        echo "stop crond server, pid="$pid"..."

        check_crond_exist
        pidIsExits=$?
        if [ $pidIsExits -eq 1 ]; then
            kill $pid
        else
            echo "crond server not exist."
            rm -f $pidPath
        fi

        waitTime=60

        hasT=1
        for i in `seq $#`; do
            param=`eval echo '$'$i`
            case "$param" in
            -t)
                hasT=0
                TValueKey=`expr $i + 1`
                TValue=`eval echo '$'$TValueKey`
                if [ ! $TValue ];then
                    TValue=0
                fi

                if [ "$TValue" -ge 0 ] 2>/dev/null ;then
                    echo "$TValue is number." > /dev/null
                else
                    TValue=$waitTime
                fi
                ;;
            esac
        done

        if [ $hasT -eq 0 ];then
            waitTime=$TValue
        fi

		isFalse=1
        try=0
        while test $try -lt $waitTime; do
            if [ ! -f "$pidPath" ]; then
                try=''
				isFalse=0
                break
            else
               kill $pid 2> /dev/null
            fi
            echo -n
            try=`expr $try + 1`
            sleep 1
        done

		if [ $isFalse -eq 1 ];then
			#强制退出模式，超时强杀
            if [ $hasT -eq 0 ] && [ $TValue -gt 0 ];then
                ps -eaf |grep "crond.php" | grep -v "grep"| awk '{print $2}'|xargs kill -9
                isFalse=0
                rm -f $pidPath
                echo "stop crond ok2."
            else
                echo "stop timeout failed."
            fi
		else
			 echo "stop crond ok."
		fi

    fi

    rm -f $myStopPidPath
	return $isFalse
}

#启动crond server
start() {
    check_mystop_exist
    stopIsExist=$?
    if [ $stopIsExist -eq 1 ];then
        echo "crond server is being stopped..."
        exit 1
    fi

    check_crond_exist
    pidIsExits=$?
    if [ $pidIsExits -eq 1 ]; then
        echo "crond server had running..."
    else
        #杀死所有残留的子进程
        #ps -eaf |grep "crond.php" | grep -v "grep"| awk '{print $2}'|xargs kill > /dev/null 2>&1
        echo "start crond server..."
        #启动并传递一个d参数作为后台进程
        cmd=$phpbin" crond.php -d"
        if [ ! -f "$cronolog" ]; then
            $cmd > $logPath$logFileName 2>&1
        else
            $cmd | $cronolog $logPath%Y%m/%d_$logFileName > /dev/null 2>&1 &
        fi

    fi
	return 0
}

#重启cron服务
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
        stop -t 1200
    else
        stop
    fi
    stopRes=$?
	if [ $stopRes -eq 0 ] && start;then
		echo "restart crond ok."
		return 0
	else
		echo "restart crond failed."
		return 1
	fi
}

#检测crond进程是否存在
check_crond_exist() {
    if [ ! -f "$pidPath" ]; then
        return 0
    fi

    pid=`cat $pidPath`
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

check_mystop_exist() {
    if [ ! -f "$myStopPidPath" ]; then
        return 0
    fi

    pid=`cat $myStopPidPath`
    pids=`ps aux | grep crond.sh | grep -v grep | awk '{print $2}'`
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

#当输入的参数为
case "$1" in
start)
    start
    ;;
stop)
    stop `echo $*|xargs -n 1|grep -v $1`
    ;;
restart)
    restart `echo $*|xargs -n 1|grep -v $1`
    ;;
*)
    echo "Usage: crond.sh {start|stop|restart|help}"
    exit 1
esac
