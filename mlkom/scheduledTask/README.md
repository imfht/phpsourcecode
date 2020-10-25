### 定时任务系统
该系统基于swoole开发，主要功能以下：
1. 添加定时任务，间隔时间最小单位为一分钟；
2. 记录每个定时任务具体运行日志；
3. 任务错误报警：服务挂掉通知运维人员，程序错误通知程序员处理

### 关于swoole介绍
基于C语言开发的PHP扩展，swoolePHP语言的高性能网络通信框架，提供了PHP语言的异步多线程服务器，异步TCP/UDP网络客户端，异步MySQL，数据库连接池，AsyncTask，消息队列，毫秒定时器，异步文件读写，异步DNS查询。

1. 官网地址：[http://www.swoole.com](http://www.swoole.com)
2. Github地址： [https://github.com/swoole/swoole-src](https://github.com/swoole/swoole-src)


### 如何使用定时任务系统
1. 启动定时任务系统  php Timer.php start

![服务器启动](http://git.oschina.net/uploads/images/2015/0527/174622_d6d58459_121747.png "在这里输入图片标题")

2. 重启worker进程   php Timer.php reload

![查看进程](http://git.oschina.net/uploads/images/2015/0527/174743_bac59713_121747.png "在这里输入图片标题")

3. 关闭任务系统      php Timer.php stop

### 后台客户端调用示例代码，参考client.php
```
$client = new swoole_client(SWOOLE_SOCK_TCP);
//发起网络连接
$ret = $client->connect('127.0.0.1', 9503, 0.5);
if(!$ret) {
    echo "Over flow. errno=". $client->errCode;
}

//添加定时任务
$data = array(
    's_id'       => 4,
    's_interval' => 3000,
    's_title'    => '我去我去',
    's_url'      => 'http://queue.caihong.com/stat/stat/login/',
    'u_id'       => 1
);
$buffer = array('type'=>'add', 'list'=>$data);

//删除定时任务
$delData = array('s_id'=>3, 's_timerId'=>3);
$buffer  = array('type'=>'del', 'list'=>$delData);

$buffer  = json_encode($buffer) . "\r\n\r\n";
$client->send($buffer);

$ret =  $client->recv();
print_r(json_decode($ret, true));

```
