#!/bin/sh

#公共库
c_notify='\E[1;36m\c'
c_error='\E[1;31m\c'
function pcecho()
{    #{{{#
    local message=$1
    local color=${2:-$black}
    echo -e "$color"
    echo -e "$message"
    tput sgr0           # Reset to normal.
    echo -e "$black"
    return
} #}}}#
function pcread()
{ #{{{#
    local color=${4:-$black}
    echo -e "$color"
    read $1 "$2" $3
    tput sgr0           # Reset to normal.
    echo -e "$black"
    return
} #}}}#
function pconfirm()
{ #{{{#
    while [ 1 = 1 ]
    do
        pcread -p "$1 [y/n]: " CONTINUE $c_notify
        if [ "y" = "$CONTINUE" ]; then
          return 1
        fi

        if [ "n" = "$CONTINUE" ]; then
          return 0
        fi
    done
    return 0
} #}}}#

function prt_help()
{ #{{{#
    echo "usage:"
    echo "cd PRJ_HOME"
    echo "$0 test|gray|online|all file1..."
    exit 1
} #}}}#
function exit_msg()
{ #{{{#
    local msg=$1
    pcecho $msg $c_error
    exit 2
} #}}}#

#确认上线环境
[[ $# -eq 0 ]] && prt_help
[[ "_test_gray_online_all_" =~ "_${1}_" ]] || prt_help
group=$1
pconfirm "确认发布环境 $group ?"
[[ $? -eq 1 ]] || exit 2
shift

#确认配置文件
conf_file=""
for name in deploy.sh deploy.conf.sh deploy_conf.sh
do
    tmp="./deploy/${name}"
    if [[ -f $tmp ]]
    then
        conf_file=$tmp
        break
    fi
done
[[ -z $conf_file ]] && exit_msg "配置文件不存在"
pcecho "配置文件：$conf_file" $c_notify

#检查配置文件是否支持
[[ $(cat $conf_file | grep -P '^group_' | wc -l) -lt 3 ]] && exit_msg "conf文件不符合规范"

#变更上线列表
sed -r -i "s/^(group_[^=]+)/#\1/g" $conf_file
if [[ $group == "all" ]]
then
    sed -r -i "s/^#(group_[^=]+)/\1/g" $conf_file
else
    sed -r -i "s/^#(group_${group})/\1/g" $conf_file
fi
source $conf_file

#选择上线方法
deploy_file=""
for name in fast_deploy.sh deploy.minor.release.sh
do
    tmp="./deploy/${name}"
    if [[ -f $tmp ]]
    then
        deploy_file=$tmp
        break
    fi
done

#记录日志
start_log_time=`date "+%Y-%m-%d %H:%M:%S"`
dlogfile="/data/log/deploy/deploy.lg"
echo -e "\nstart--[$start_log_time]" >> $dlogfile
echo -e "project: `pwd -P`" >> $dlogfile
echo -e "group: $group" >> $dlogfile
echo -e "hosts: $online_clusters" >> $dlogfile
echo -e "files: $1" >> $dlogfile

#上线
sh ${deploy_file} $@

#还原
svn revert $conf_file > /dev/null#!/bin/sh
