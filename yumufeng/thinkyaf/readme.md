由于个人的项目需要，而对国产框架thinkphp又情有独钟，利用yaf的高性能，整合了一些网上的轮子和API接口需要的组件，致力于提高生产环境下的运行性能和开发环境下的开发效率。
核心库高效封装常用库和操作,兼容php5.6及以上，在PHP7.1上性能爆发，不知所命何名甚好，暂叫 thinkyaf 。

# 推荐安装环境

 1.yaf >= 3.0.6
 
 2.PHP版本 >= 7.0
 
 3.Nginx 1.12.0
 
 4.mysql >= 5.6

支持以下特性

 **1. 数据库ORM**
 *   基于ThinkPHP5.1的ORM独立封装，PDO底层
 *   支持Mysql、Pgsql、Sqlite、SqlServer、Oracle和Mongodb
 *   支持Db类和查询构造器
 *   支持事务
 *   支持模型和关联
 
 **2. 缓存** 

*   驱动方式（支持file/memcache/redis/xcache/wincache/sqlite）

使用File作为缓存驱动时，请设置 runtime目录为 777 可读可写权限

**3 .数据验证**

**4 .Yar RPC接口开发**

**5 .Restful接口设计**

#封装了以下轮子

* 非对称加密库 - Rsa
* 快速随机数生成器 - Random
* 输入过滤库 - input
* 微信小程序类 - Weapp
* Cookie和Session的操作
* Mail邮件类 - 发送验证码\通知等
* 支付宝、微信支付接口


更多用法，见文档：

看云文档地址：https://www.kancloud.cn/yumufeng/thinkyaf
