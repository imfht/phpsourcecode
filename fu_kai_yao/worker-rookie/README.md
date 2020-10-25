# worker-rookie
[github地址](https://github.com/mingdiantianxia/worker-rookie)

## 关于本项目
**基于swoole的轻量级异步任务框架，10分钟即可搭建你的异步任务服务**

**author：fukayao**

**date：2020-4-14**

**email：1982104592@qq.com**

## 环境依赖

* PHP版本大于等于 5.6 (推荐PHP7及以上版本)  
* Posix、Pcntl和Swoole扩展  
* Swoole扩展版本不小于1.9.18（定时任务-进程模式；推荐使用进程模式）
* Swoole扩展版本不小于4.2.9（定时任务-协程模式；需要PHP7以上版本）

## 致谢

* Medoo  轻量级的PHP数据库框架！文档地址https://medoo.lvtao.net/1.2/doc.php
* Swoole文档地址 https://wiki.swoole.com/wiki/index

## 功能

* 多应用配置管理
* 命名空间自动加载
* redis缓存
* 自定义路由
* 日志分割
* 业务模块数据隔离
* 秒级定时任务（支持协程与进程两种模式）
* 消息队列服务（支持多进程消费消息）

## 目录结构
```code
├─apps                   应用层目录
│  ├─api                 api应用目录  
│  ├─console             命令（定时任务）应用目录
│  
├─config                 配置目录
├─router                 api路由配置目录
├─runtime                运行和日志目录
├─scripts                服务脚本目录
├─system                 系统层目录
│  ├─commons             系统公共目录  
│  ├─datalevels          数据目录
│  ├─services            业务逻辑目录
│  
├─workerbase             框架基础类库目录
```

## 项目部署

1、修改config.php配置文件中的php命令路径
```code
    //php命令路径
    "phpbin" => "/usr/local/php/bin/php",
```
2、修改config.php中的mysql数据库和redis连接，或者用env.php覆盖默认配置
```code
    'db'=> [
        'database_type' => 'mysql',
        'database_name' => 'test',
        'server' => '192.168.1.219',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
        // 可选参数
        'port' => 3306,
        // 可选，定义表的前缀
        'prefix' => '',
    ],

    'redis' => [
            //redis服务器地址
            'host'  => '192.168.1.219',
            //redis端口
            'port'  => '6379',
            //redis密码
            'password' => '',
            //连接超时
            'timeout' => 10,
            //持久链接
            'persistent' => true,
    ],
```

3、运行服务
```code
    cd scripts/
    
    //运行定时任务
    bash crond.sh start
    //停止定时任务（后面加-t 1200参数，表示1200秒超时强制退出）
    bash crond.sh stop
    //重启定时任务（后面加-t参数，执行超时强制重启）
    bash crond.sh restart
    
    //运行队列服务
    bash workerServer.sh start
    //停止队列服务（后面加-t 1200参数，表示1200秒超时强制退出）
    bash workerServer.sh stop
    //重启队列服务（后面加-t参数，执行超时强制重启）
    bash workerServer.sh restart
    
    //同时运行定时任务与worker队列服务
    bash server.sh start
    //同时停止定时任务与worker队列服务（后面加-t参数，后台执行超时强制退出）
    bash server.sh stop
    //同时重启定时任务与worker队列服务（后面加-t参数，后台执行超时强制重启）
    bash server.sh restart
    
    
```

4、无人值守
```code
  //linux中/etc/crontab添加定时任务，
  #每分钟尝试启动一次swoole定时任务和worker队列服务
  */1 * * * * root bash 项目根目录绝对路径/scripts/server.sh start
  
  //或者直接运行脚本安装以上内容
  ./installCrontab
```

5、nginx路由重写
```code
   location / {
           try_files $uri $uri/ /index.php$is_args$args;
       }
```