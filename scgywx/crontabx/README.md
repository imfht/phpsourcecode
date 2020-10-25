CrontabX是PHP写的计划任务与守护进程管理器，只需要简单配置即可，在CrontabX里所有的任务都是由子进程来执行。   
所有守护进程会在程序启动时全部启动，并且会监控所有子进程，一旦有子进程退出，则会创建新进程继续执行。   
当计划任务到了执行时间，则会创建一个子进程来运行指定脚本。 

#基本配置
```php
array(
	'pidfile' => '/tmp/crontab.pid', //pid路径
	'logfile' => '/tmp/crontab.log', //日志文件
	'loglevel' => 2, //日志等级
	'daemonize' => true,//是否守护进程
	'crontab' => array(//crontab与daemon列表配置(见下面说明)
	)
)
```

# crontab
这个类似linux自带的crontab，并且格式都完全模仿，只是多了一个精确到秒的控制(当然这是可选的)  
```php
array(
	'name' => 'test2',
	'script' => 'crontab/test2.php',//指定脚本
	'wakeup' => '* * * * * *',//crontab格式：[秒 ]分 时 天 月 周(秒可以不填)
)
```

想要每5秒运行一次，就是这样：
```php
array(
	'name' => 'test2',
	'script' => 'crontab/test2.php',
	'wakeup' => '*/5 * * * * *',
)
```
# daemonize
这个就是守护进程了， 
```php
array(
	'name' => 'test',
	'script' => 'daemon/test.php',//指定脚本
	'childs' => 1,//子进程数量
	'daemon' => 1,//是否守护进程
)
```

# 运行
```php
php crontab.php config.php
```
