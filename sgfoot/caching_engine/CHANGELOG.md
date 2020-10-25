CHANGELOG
=========

2018-05-03 v1.1.0
------------------
- 修复*Store.php构造函数第一个参数形参的类型定义
- 解决redis存储数组异常

2018-04-19 v1.0.9
------------------
- 解决报Undefined class constant 'SERIALIZER_IGBINARY'
- [解决方案](http://www.sgfoot.com/view/125)

2018-04-19 v1.0.8
------------------
- 修改memcached get返回false的bug,空值应为null

2018-04-16 v1.0.7
------------------
- 新增redis引擎
- 添加DCache,RCache快捷操作
- DCache 是memcached的快捷操作 MCache是memcache的操作,请注意区分
- 支持原生对象的方法调用(非常实用)

2018-04-09 v1.0.6
------------------
- 新增memcache方法
- 配置节点file,memcache,memcached
- 加入Connector单例模式,提高性能
- 加入垃圾回收机制(需主动调用)
- 新增memcache图形管理

2018-03-30 v1.0.5
------------------
- FileStore支持igbinay序列化,提高性能
- memcache 升级为memcached扩展,将更高效
- memcached 支持分布式部置

2018-03-29 v1.0.4
------------------
- 添加一个MCache文件,对memcache快捷操作
- MCache::put('key', 'abc');//相当于Cache::store('memcache')->put();
- MCache::get('key');//相当于Cache::store('memcache')->get();

2018-03-29 v1.0.3
------------------
- FileStore引擎存储文件改成无后缀的文本类型,不再以php做后缀了
- 在StoreAbstract方法定义一个info抽象方法,用法获取引擎详情
- StoreAbstract里的remember,rememberForever传入的数据不强制是匿名,修改成两种兼容,可是任意数据或匿名

2018-03-29 v1.0.2
------------------
添加更多实例

2018-03-29 v1.0.1
------------------
memcached配置config.php项目添加is_zip,zip_level选项

2018-03-27 v1.0.0
------------------
php-ico-cache上线