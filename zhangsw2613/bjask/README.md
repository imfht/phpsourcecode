# bjask
### 一个基于swoole开发的多进程任务系统
### 另写了个使用swoole协程开发的laravel扩展包，有兴趣的移步：https://gitee.com/zhangsw2613/laravel-bjask
### 1. 安装方法
    安装composer
    执行composer install


### 2.启动
    php server.php 列出所有可使用的命令
    给队列增加一个消息，使用http请求项目目录下 send.php


### 3.任务处理
	app目录下
		Configs		 	系统/项目配置文件
		Controllers 		控制器层
		Libs 			第三方扩展类库
		Models			数据模型层，具体数据使用方法可阅读https://www.doctrine-project.org/projects/doctrine-orm/en/latest/tutorials/getting-started.html


### 4.后续
	...
