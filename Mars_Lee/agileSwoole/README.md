# 关于Agile Swoole

一个高性能的PHP开发框架（swoole）

##
    特性
        1. 支持MVC
        2. 支持自定义常驻进程
        3. 支持多种任务模式
        4. 路由自定义事件
        5. 简单易用orm[可二次开发，实现接口，自动注入即可]
        6. 自动协程（Coroutine，假如你的swoole是2.0以上，自动开启协程进行调度）
        7. 支持yaf
        8. 全面支持psr container psr http-message psr autoloader
        9. 分布式（待开发）
        10. 队列（待开发）
        
## 压力测试

#### 测试机器
     
    型号名称：	MacBook Pro
    型号标识符：	MacBookPro14,1
    处理器名称：	Intel Core i5
    处理器速度：	2.3 GHz
    处理器数目：	1
    核总数：	    2

    
#### 测试命令

    cd bin
    php agile.php
    ab -c 100 -n 50000 http://127.0.0.1:9550/welcome
    
#### 测试结果

```
Server Software:        swoole-http-server
Server Hostname:        localhost
Server Port:            9550

Document Path:          /welcome
Document Length:        2236 bytes

Concurrency Level:      50
Time taken for tests:   1.684 seconds
Complete requests:      10000
Failed requests:        0
Total transferred:      23860000 bytes
HTML transferred:       22360000 bytes
Requests per second:    5936.99 [#/sec] (mean)
Time per request:       8.422 [ms] (mean)
Time per request:       0.168 [ms] (mean, across all concurrent requests)
Transfer rate:          13833.66 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0    2  12.4      2     620
Processing:     0    6  41.5      3     624
Waiting:        0    5  38.7      2     624
Total:          0    8  43.4      5     627
```   
        
## 快速开始

    composer require fresh-li/agile-swoole:dev-master
    cd bin
    php agile.php
    
    http://127.0.0.1:9550/welcome
	
## 路由

```
    CONF_PATH/route.php
    [
        'path'          =>      '/',
        'dispatch'      =>      [\Controller\Welcome::class, 'index']
    ],
    [
        'path'          =>      '/sync',
        'dispatch'      =>      [\Controller\Sync::class, 'run'],
        'type'          =>      \Component\Producer\Producer::PRODUCER_SYNC
    ],
    [
        'path'          =>      '/process',
        'dispatch'      =>      [\Controller\Process::class, 'run'],
        'before'        =>      [\Controller\Process::class, 'before'],
        'after'         =>      [\Controller\Process::class, 'after'],
        'type'          =>      \Component\Producer\Producer::PRODUCER_PROCESS
    ]
    
    GET: localhost:9550
    hello world!
    
    GET: localhost:9550/sync
    sync start
    ... 10 seconds after
    sync over
    
    POST: localhost:9550/process
    this process berfore
        create process ......
    this process after
```

## 3种不同的触发模式
```
    class Sync{
        public function index()
        {
            return 'ff';
        }
    }
    
    {"code":0,"response":"ff"}
    
    class Process{
            public function index()
            {
                return 'ff';
            }
    }
    {"code":0,"response":{"processId":"{$processId}"}}
    
    class Task{
            public function index()
            {
                return ff;
            }
    }
    {"code":0}
```

## 常驻内存任务,开启服务立马启用
    
```
    $serverProcess = new ServerProcess();
    $serverProcess->addProcess(function(){
        while(true){
            //do some things
        }
    });
```


## 支持yaf


```
Server Software:        swoole-http-server
Server Hostname:        localhost
Server Port:            8100

Document Path:          /welcome
Document Length:        12 bytes

Concurrency Level:      50
Time taken for tests:   1.381 seconds
Complete requests:      10000
Failed requests:        0
Total transferred:      1600000 bytes
HTML transferred:       120000 bytes
Requests per second:    7243.39 [#/sec] (mean)
Time per request:       6.903 [ms] (mean)
Time per request:       0.138 [ms] (mean, across all concurrent requests)
Transfer rate:          1131.78 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0    0   0.1      0       2
Processing:     1    7   0.8      7      13
Waiting:        0    7   0.8      7      13
Total:          3    7   0.7      7      13
```

## orm

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).