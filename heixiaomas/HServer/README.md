# HServer

#### 介绍
    QQ交流群：1065301527
    基于 Workerman 而做的一款高并发 WebServer。单机2G 4核 7W+QPS
    用他我们能干啥，做一个最求性能切功能简单的程序。麻雀虽小，但是以为我公司，完成短视频接口后台，口令后台的基本业务的程序支撑
    如果你是一个Javaer或者喜欢java，可以关注下 
[![黑小马/HServer](https://gitee.com/HServer/HServer/widgets/widget_card.svg?colors=4183c4,ffffff,ffffff,e3e9ed,666666,9b9b9b)](https://gitee.com/HServer/HServer)

#### 运行方式

```shell
# 启动
php start.php start

# 停止
php start.php stop

# 状态查询
php start.php status

# 重启
php start.php restart

# 平滑重启
php start.php reload
```

#### 配置文件

关于 redis 和 mysql 在 [Hserver/config/config.php](HServer/config/Config.php)，配置好后开启就可以在控制器中使用了。

#### 编写代码

##### 1.目录介绍和启动测试
```
├─app               #app是我们开发用最多的文件夹
│  ├─action         #控制器编写目录
│  ├─filter         #拦截器目录    
│  ├─static         #静态文件目录
│  ├─task           #定时任务目录
│  └─view           #smart模板目录    
├─HServer           #HServer核心逻辑
├─log               #程序跑飞时产生的日志文件
├─templates_c       #模板生成的缓存目录
└─vendor            #框架库

```
- 更具平台对应启动HServer
- 访问测试地址：  
    http://127.0.0.1:8800/index/main  
    http://127.0.0.1:8800/index/html
- 你将看到最基本的HelloWord

##### 2.项目架构
    
![AB测试](https://gitee.com/heixiaomas/HServer/raw/master/app/static/img/f.png)

##### 3.路由规则
    从app/action/开始进行规制计算
    例子1：
        app/action/index.php
        index.php 里面有一个show()方法
    url:
        http://127.0.0.1/index/show
##### 4.控制器Action编写规则
    
    1，文件必须必须放在/app/action/目录里面
    2，该文件必须是一个类，同时继承HActionView类            
    3， 父类有很多方法封装，可以直接使用
        $Response，$Request，$DB,等等，具体看文件配置
    
##### 5.拦截器Filter编写规则     
     1，文件必须必须放在/app/filter/目录里面
     2，该文件必须是一个类，同时继承HServerFilter类     
     4  $level级别定义 设置优先级，数字，越大，越先     
     5， 父类有很多方法封装，可以直接使用
         $Response，$Request，$DB,等等，具体看文件配置
            
##### 6.定时器task编写规则  
    1，文件必须必须放在/app/task/目录里面
    2，该文件必须是一个类，同时继承HServerTask类     
    4 ,$time 延时定义单位秒   
    5，父类有很多方法封装，可以直接使用
        $DB,等等，具体看文件配置

#### 更新日志

请查看 [CHANGELOG.md](CHANGELOG.md) 了解近期更新情况。
