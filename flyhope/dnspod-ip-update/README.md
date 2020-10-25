# dnspod-ip-update

本程序可以获取你的电脑的IP地址，并更新至DNSPOD中，主要用于在内网环境下需要连接主机，IP却经常在变的情况。同时也支持自动更新公网IP于DNSPOD中。

# 依赖
- PHP >= 5.4
- PHP CURL扩展

# 自动使用公网IP更新方法（一般作为建站、提供公网服务使用）

1. 下载此代码，复制config.inc.sample.php至config.inc.php，填写空白内容。
2. 添加Crontab（WINDOWS则为计划任务），设置每1-5分钟执行一次 ``` php {安装路径}/ddns.php ```。

# 获取网卡IP更新方法（一般作为解析为内网IP使用）

## 安装配置

1. 下载此代码，复制config.inc.sample.php至config.inc.php，填写空白内容。
2. 添加Crontab（WINDOWS则为计划任务），设置每1-5分钟执行一次更新脚本。

## 更新脚本

CentOS/Redhat系统，未禁用 shell_exec 函数
```bash
php linux.php
```

Windows系统，未禁用 shell_exec 函数，在代码目录执行

非Server操作系统，必需以Administrator身份执行set-executionpolicy remotesigned后才可以

```bash
windows.ps1
```

其它操作系统或禁用shell_exec函数
```bash
获取IP命令 | php load.php
```
如Linux
```bash
/sbin/ifconfig -a|grep inet|grep -v 127.0.0.1|grep -v inet6|awk '{print $2}'|tr -d "addr:" | php load.php
```
或
```bash
ip a |fgrep inet|fgrep -v inet6|fgrep -v 127.0.0.1|fgrep -v docker|awk ' {print \$2} '|awk -F '/' '{print \$1}' | php load.php
```
