# PhaService

## Phalcon + Swoole 无侵入解决方案

Phalcon有着强大的性能同时又具备完整的MVC模式,    
Swoole也具备在Phalcon之外的其他能力,如果把两者无缝的结合,   
一定是一个不错的案例.   

所以本项目 同时支持 Nginx+Phalcon 与 Swoole+Phalcon,   
如果使用Nginx做负载均衡,可以做到无缝衔接,有Nginx+php-fpm的稳定,   
同时也能享受Swoole对于API的超高性能.   

本案例可以作为系统服务使用, 也可以做Restful开发使用,作为Web使用更是毫无问题. 

使用 wrk 做的的压测, 在MBP上的结果:

```bash
wrk -c10000 -d10s --latency  http://127.0.0.1:8080/testRunning 10s test @ http://127.0.0.1:8080/test
  2 threads and 10000 connections
  Thread Stats   Avg      Stdev     Max   +/- Stdev
    Latency    38.66ms   50.01ms 266.35ms   81.83%
    Req/Sec    12.97k     1.54k   16.65k    88.17%
  Latency Distribution
     50%   11.53ms
     75%   68.33ms
     90%  116.48ms
     99%  183.72ms
  242375 requests in 10.06s, 36.59MB read
Requests/sec:  24104.01
Transfer/sec:      3.64MB
```

非常不错的结果. 

## Installation

** Web服务兼容Nginx+PHP-FPM模式,可以参考Phalcon的Nginx配置.

*依赖*:

> Ubuntu 16.04LTS/18.04LTS   
> PHP: 7.0+, 推荐 7.2或以上    
> Beantalkd 队列处理依赖    
> php扩展 Phalcon 3.x+    
> php扩展 Swoole 2.x+    
> php扩展 Redis  
> php扩展 Beanspeak  
> php扩展 Pdo,Pdo MySQL  

####初始化

```bash
composer install -o
```

*开启 HttpServer 服务:*
建议使用Nginx做负载均衡,使PHP-FPM可以和Swoole的HttpServer同时提供服务. 

```bash
./web_serve start
```

可以使用`sys/systemd/StdWebServer.GenService.php`可以生成`systemd service`文件,
根据提示安装成服务.

```bash
cd sys/systemd/
php StdWebServer.GenService.php
```

*开启 WebSocketServer 服务:*
```bash
./web_socket_serve start
```

可以使用 `sys/systemd/StdWebSocketServer.GenService.php` 可以生成`systemd service`文件,
根据提示安装成服务.

```bash
cd sys/systemd/
php StdWebSocketServer.GenService.php
```


## Configuration
#### 环境配置
在项目的/目录下,建立空文件 `.development` 或 `.testing`则指定里开发环境与测试环境, 没有文件为生产环境. 文件同时存在, 有限开启开发环境. 
```
#开发环境
rm .testing && touch .development 

#测试环境
rm .development && touch .testing 

#生产环境
rm .development .testing 
```

#### 数据库,Redis等配置:
Web与Cli分开配置,配置文件位于:
* App部分: `/app/config/config.php`   
* Cli部分: `/cli/config/config.php`

#### Swoole Http Server 配置:
* 配置文件: `/sys/config/std_web_server.php`

#### Swoole WebSocket Server 配置:
* 配置文件: `/sys/config/std_web_socket_server.php`


## Features
* Phalcon 完整支持  
* Http 服务器
* WebSocket 服务器
* 多进程Task Worker 任务处理  
* Beantalk 队列
* Systemd自启服务


## Documents

###使用Composer建立项目
'''bash
composer create-project smices/pha-service -s dev
'''

###多进程Task任务处理
该服务会在任务处理完成后,持续拉起服务,所以可以实现类似php-fpm的特点, 任务处理指定次数后退出任务,服务会自动拉起服务.   
具体可以参考/cli/tasks/MailSenderTask.php, 复写 RealWork 函数进行真实的任务处理即可, 调用方式:   
```bash
#查看帮助信息
./run mailsender -h

#参数 6 为开启6个子进程同时处理任务
./run mailsender main 6 

#查看队列任务数量
./run mailsender info

```
添加数据到邮件队列:
```php
<?php
$mq = new Beanspeak\Client(['host' => '127.0.0.1', 'port' => '11300']);
$data = [
    'to'      => ['jacky.ju@qq.com', 'JACKY JU'],
    'subject' => '测试标题',
    'content' => 'HELLO'
];
$mq->putInTube('MAIL_SENDER', $data);
```

** Beantalk 队列可以使用 https://github.com/xuri/aurora 可视化工具进行操作管理.


### Web Socket 的使用
WebSocket 使用Task DI, 所以控制器位于 /cli/wsockets/目录下.
示例可以查看 /cli/wsockets/MainTask.php
```php
//...
public function mainAction()
{
    // 参数解析,务必加上,放在initialize中,只有mainAction生效,其他Action无效,所以请务必啰嗦的加上
    // 也有可能是我的Phalcon版本问题
    $this->parseArguments(); 
    
    // 请在这里写入真实逻辑业务处理
    
    //将结果返送给客户端, 发送内容是String, Int, Object都行
    $this->send('WELCOME');
}//end
//...    
```

发送指令示例:
JSON格式转字符.可以使用JSON.stringify({...})把Object格式转换成String格式   
cmd: Task.Action的组合,小写.   
argv: 要传递给Action的参数. 可以在Php中用 $this->params访问   
```javascript

//...
websocket.send('{"cmd":"main.whoami","argv":"anything"}');
//...

```

返回格式示例:
cmd: 调用的原始命令,在于异步任务处理是,可以根据cmd找回呼叫的指令   
ret: 处理结果
```json
{"cmd":"main.whoami","ret":3}
```

