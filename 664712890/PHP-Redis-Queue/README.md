#PHP Redis Queue

这是一个可以扩展的队列处理程序，没有使用框架。我们的项目使用Yii的框架，所有这里的测试示例就按照Yii的方式来编写的。

###简要设计思路
1. 存入的将数据分散到64个Redis的list中去，这样是便于扩展机器来提高负载
2. 后台运行一个PHP主进程，分别fork 64个子进程去对应处理 64个list中的数据。
3. 主进程要监控子进程状态，在子进程正常或意外结束后及时重新开启对应子进程。

###现状
在我们项目中已经使用一年多，性能和稳定性方面都表现的很不错。
目前是单机运行64个进程，因为单机处理效率已经能够满足需求，就没有在处理程序这个理做更多分布式开发了。

#开发与使用

使用上参考 test/QueueCommand.php 主要是3个命令：start，stop，restart 非Yii框架参考这个文件编写代码即可。
test/queue_config.php 是配置文件，使用前请先看看。

###设置配置文件路径
```
// Yii 这样配置：
Yii::app()->params->queueConfig;

// 通用的配置方式：
QueueConfig::$configPath = '/data/www/test/queue_config.php';
```

###开发自己的worker处理程序
参考 src/worker/CWorkerForYii.php 从CWorker继承，然后实现相应的方法即可。
当然，你需要在配置文件中配置你的类文件名称：'WORKER'=> 'CWorkerForYii',
