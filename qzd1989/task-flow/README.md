# task-flow
***
```自定义后台任务流```
***
# 开发环境
***
```PHP 7.2.8 (cli)```
```mysql  Ver 14.14 Distrib 5.7.23```
***
#数据结构为:
***
```任务表 task```
```子任务表 task_sub```
***
#使用方法:
***
```建库,再导入Config/init.sql,再配置Config/Database.php;```
***
```命令行执行 php -f pathTo/Console/Example.php Hello 即可插入一个Hello模板的任务```
***
```命令行执行 php -f pathTo/Console/Run.php & 即可创建常驻进程的任务消费脚本```
***
```命令行执行 php -f pathTo/Console/Run.php 1 即可执行单个任务的最新子任务, 1指的是task表中的主键```
***
```请参照pathTo/Console/Example.php编写新的任务流```
***
```支持任务流中的分裂成多个子任务流```