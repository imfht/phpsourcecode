#!/bin/sh

# 定义函数


# 确定目录与git仓库
# if [[ $PWD =~ '/alidata/www' ]]
# then
#     echo "包含"
# else
#     echo "不包含"
# fi


configFile='/alidata/www/demo.bauth.cn/runtime/gitPullSwitch'
# GIT webhook更的git版本文件， 不用mysql等db，用file
if [ -s $configFile ]
then
	cd /alidata/www/demo.bauth.cn
	git pull
	echo > $configFile
fi
