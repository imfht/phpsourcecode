Slob框架
----
本框架基于Swoole Framework开发，后台模板使用DWZ JS前端框架，详情请参考如下

## Swoole参考
文档位置：(https://github.com/swoole/framework)
官方地址：(http://wiki.swoole.com/wiki/index/prid-2)

## DWZ参考
DWZ官方DEMO：(http://www.j-ui.com/)

特色
----
* 自动后台生成，可根据数据库表类型生成表单
* 后台完全JS实现交互，不用写一行JS

开发环境配置
----
* 数据库文件food.sql
* 登录用户名:test 密码:test
* 进入默认生器页面  开发时保留  上线删除
* 配置数据库文件参考apps/configs/dev/目录下（同时要在php.ini 中设置 env.name=dev）
* php 版本5.6+ 低版本暂不支持 因为其中  array  以都替换为 []
* 不需要niginx或者apache 利用自身php -s 实现web 服务器，windows直接执行start.bat 即可
* linux下请自行配置nginx 配置写了  相信用linux 的你比我熟

总结
----
我就是懒，不一样的懒！为了懒人设计，完全解放双手。

版本
----
* v1.0.0 内嵌框架版本
* v2.0.1 使用composer管理依赖