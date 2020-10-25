# 基于thinkphp5_layui秒级定时任务管理

**当前版本每次执行任务都会fork进程，比较浪费系统资源。需进程池版本请切换分支multi-task-version**

jtimer使用了master-worker进程模型，能够实现无阻塞执行任务。

时间表达使用了cron表达式，可精确到秒级，方便好用（比crontab多一位）

# JAVA版
https://gitee.com/itzhoujun/JTimer-java

JAVA版和PHP版功能基本一致，仅仅语言和实现机制不同。

# 安装与使用

## 项目要求：
1. php.ini开放exec方法
2. 安装pcntl扩展
3. 安装posix扩展

ps:仅支持Linux

## 后台部署

项目后台基于thinkphp5+layui实现，部署方法参考thinkphp5官方文档，此处不再阐述。

数据库文件位于项目根目录 jtimer.sql，请自行导入

默认用户名密码：admin/admin


## 任务进程管理
所有命令均在项目根目录下执行

启动进程：（守护进程模式）
> php think jtimer start -d

启动进程：（调试模式）
> php think jtimer start

停止进程：
> php think jtimer stop

查看进程状态：
> php think jtimer status 或 ps aux | grep jtimer

# 架构介绍

## cron表达式
```
* * * * * *
| | | | | |
| | | | | ---- 星期（0-6）  
| | | | ------ 月份（1-12）
| | | -------- 日  （1-31）
| | ---------- 时  （0-23）
| ------------ 分  （0-59）
|------------- 秒  （0-59） 
 ```

## 进程模型

![进程模型](https://gitee.com/uploads/images/2018/0403/112409_c645d92d_369962.png "未命名文件 (1).png")

简单来说，就是两个worker进程，1个负责数据的读写（读任务，写日志），1个负责任务的执行（创建task进程执行任务）。

两者之间通过tp框架自带的文件缓存作为沟通的桥梁。

Q1： 为什么要用两个worker，而不是一个worker直接读数据库然后执行任务？

> 为了让任务不阻塞，执行每一个任务时都会创建一个新的task进程去执行，task进程执行完毕会退出。如果在worker进程使用了数据库连接，那么fork出来的task进程会继承worker进程的连接（共用一个数据库连接），在task进程退出后，worker和task共用的连接也将被关闭，导致worker断开数据库连接。

Q2：cron任务定时执行是如何实现的？

> 先解析任务的cron表达式得到该任务下次要执行的具体时间，然后将该任务置于时间轮片（TimingWheel）中，worker进程每秒查看一次时间轮片，发现有要执行的任务就取出来执行。执行完毕后再重复执行上面的步骤。（关于TimingWheel，请自行百度）

# 演示

![输入图片说明](https://gitee.com/uploads/images/2018/0403/114238_09c5b565_369962.png "TIM截图20180403113947.png")

![输入图片说明](https://gitee.com/uploads/images/2018/0403/114247_fed9251f_369962.png "TIM截图20180403114002.png")

![输入图片说明](https://gitee.com/uploads/images/2018/0403/114256_3f9a3561_369962.png "TIM截图20180403114154.png")

![输入图片说明](https://gitee.com/uploads/images/2018/0403/114305_dd1f5c3b_369962.png "TIM截图20180403114207.png")

![输入图片说明](https://gitee.com/uploads/images/2018/0403/114312_49078c1a_369962.png "TIM截图20180403114215.png")

# 注意
1. 本项目只在测试环境运行过，如果要用于生产环境，请自行进行严格的测试后再投入使用。如有问题，自行负责。
2. 定时器每秒有零点几毫秒的误差，几个小时累计下来可能有1秒的误差。因此要求时间非常精准的业务任务请不要使用本系统(JAVA版无此问题)。