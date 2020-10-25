# 不再维护了，这种方式的定时任务方式太容易出问题，对不起各位了

# cronManager

# 简介

cronManager是一个纯PHP实现的定时任务管理工具,api简单清晰,采用的是多进程模型,进程通信采用的是消息队列,任务监控也提供了简单的命令,方便易用

# 特性

* 多进程模型

* 支持守护进程

* 平滑重启

* 提供各种命令监控任务运行状态

* 兼容部分crontab语法

* 支持web可视化管理


## 更新日志

1. 时间设定支持crontab格式,更加灵活(`2018年01月05日`)
2. [支持thinkphp5](https://gitee.com/jianglibin/cron-manager/tree/master/doc/thinkphp5)(`2018年1月6日`)
3. 优化底层架构,优化消息队列稳定性, 增加STOP命令
4. 增加了针对任务的命令,增加了web可视化页面操作案例demo(`2018年01月17日`)
5. 优化命令行下的提示风格,优化了一些问题(`2018年01月20日`)
6. `v1.4.3` 优化稳定性,修复一些bug(`2018年1月25日`)
7. `v1.5.0` 支持更完整的crontab格式命令,以前的[分钟 小时 日期 月份]升级为[分钟 小时 日期 月份 星期], `升级后需要修改有用到crontab格式的任务,否则会报错!`
8. `v1.5.1` 优化crontab格式解析类,修复闰月计算下次运行时间报错问题
9. `v1.5.2` 优化crontab格式解析类,修复星期解析错误的问题,解决解析全部为'*'的情况下效率低下的问题

## crontab格式解析说明

* crontab示例

```
* * * * * 每分钟执行一次
0 1,3,5 * * * 每凌晨1,3,5点整运行一次
0 0 * * 5 每周星期5,0点0分运行一次
0 0 1-13 * * 每月1的13号0点0分运行一次
0 0-5/2,10,12 * 2 5 获取二月份的星期五 时间为0,2,4,10,12运行一次
```

## 环境要求

* `liunx`
* `pcntl扩展开启`
* `php 5.4以上`
* `composer`


## 安装

* `composer`安装

> composer require godv/cron-manager

## 可能遇到的问题,例如更新完版本

* `stop STOP restart -d`等命令无效..

```
# 提示成功
php tests/test.php stop 

# 提示失败
php tests/test.php -d

Starting cron-manager:	[  NO  ]
	Faild: /tmp/cron-manager-3030e2bad011676a.pid already exist!
```
解决问题步骤:

1. 杀死可能残留的worker进程 命令: `killall -9 php`

2. 清除pid文件 命令: `rm /tmp/cron-manager-3030e2bad011676a.pid` 

> 根据-d提示的文件名 Faild: `/tmp/cron-manager-3030e2bad011676a.pid` already exist!

## 使用介绍

核心方法 `CronManager::taskInterval($name, $command, $callable, $ticks = [])` 

参数1 `string` $name 定时任务名称

参数2 `string` $command 

`方式一`: 兼容部分crontab格式的语法, 粒度最小为`分钟`, 支持 `[分钟 小时 日期 月份 星期]`也就是 `* * * * *` 

`方式二`: 

使用key@value的形式表示, 不懂请看下面的`入门示例!!`
1. `s@n` 表示每n秒运行一次 
2. `i@n` 表示每n分钟运行一次 
3. `h@n` 表示每n小时运行一次
4. `at@nn:nn` 表示指定每天的nn:nn执行 例如每天凌晨 at@00:00

参数3 `callable` $callable 回调函数,也就是定时任务业务逻辑

参数4 `array` $ticks 用于单任务多进程时标识

## 快速入门示例

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

$manager = new SuperCronManager\CronManager();
$manager->workerNum = 10;

// 设置输出重定向,守护进程模式才生效
$manager->output = './test.log';
// crontab格式解析
$manager->taskInterval('每个星期5凌晨运行一次', '0 * * * 5', function(){
	echo "每个星期5凌晨运行一次\n";
});

$manager->taskInterval('每天凌晨运行', '0 0 * * *', function(){
	echo "每天凌晨运行\n";
});

$manager->taskInterval('每秒运行一次', 's@1', function(){
	echo "每秒运行一次\n";
});
$manager->taskInterval('每秒运行一次', 's@1', function(){
	echo "每秒运行一次\n";
});

$manager->taskInterval('每分钟运行一次', 'i@1', function(){
	echo "每分钟运行一次\n";
});

$manager->taskInterval('每小时钟运行一次', 'h@1', function(){
	echo "每小时运行一次\n";
});

$manager->taskInterval('指定每天00:00点运行', 'at@00:00', function(){
	echo "指定每天00:00点运行\n";
});

$manager->run();

```

## cli示例
![image.png](http://upload-images.jianshu.io/upload_images/1791210-5732b338a194023d.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)


## 参数命令大全
* `-d` 守护进程化
* `status` 查看任务状态
* `stop` 平滑停止
* `STOP` 强制停止 
* `restart` 平滑重启(`注意, 此命令仅仅是重启worker进程,修改定时任务代码,必须stop才能生效`)
* `check` 检查扩展情况 

### 命令使用场景

* **想停掉id为1,2,3的任务** (`命令语法: stop:n[,n,n]`)
> php test.php stop:1,2,3
* **想删除id为1,2,3的任务,不想在status命令中看到它** (`命令语法: STOP:n[,n,n]`)
> php test.php STOP:1,2,3
* **开启用stop命令停止的任务,id为1,2** (`命令语法: start:n[,n,n]`)
> php test.php start:1,2
* **设置的定时任务时间太长了,想现在就运行一下,id为1** (`命令语法: run:n[,n,n]`)
> php test.php run:1

### cli下太麻烦了,运维大哥都看你不顺眼了,叫你自己想办法把这玩意弄到后台管理去,到更新代码的时候再烦他

[可视化操作界面DEMO](https://gitee.com/jianglibin/cron-manager/tree/master/doc/web-cronmanager)
