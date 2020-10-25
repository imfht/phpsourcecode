# MysqlPool

#### 项目介绍
基于swoole4实现的协程数据库连接池

#### 软件架构
软件架构说明


#### 安装教程

1. 安装php7以上版本
2. 安装swoole4

#### 文件解读

1. AbstractPool.php 连接池封装抽象类
2. MysqlPoolCoroutine.php 继承AbstractPool.php，实现协程Mysql客户端的连接池Demo
3. MysqlPoolPdo.php 继承AbstractPool.php，同步PDO Mysql客户端的连接池Demo
4. channelDemo.php swoole/channel通道例子
5. CoroutineMySql.php swoole协程Mysql客户端Demo
6. Fpm.php Fpm+Mysql长连接的连接池

#### 更多文档说明，参考我的博客
(https://my.oschina.net/u/2394701/blog/2046414)