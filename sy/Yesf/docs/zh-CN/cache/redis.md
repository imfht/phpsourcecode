---
title: Redis
lang: zh-CN
---

# Redis

缓存至Redis

## 配置

配置连接池：（下列中“re”表示组件名称，在设置默认缓存组件时使用）

```ini
connection.re.driver=redis
connection.re.adapter=redis
connection.re.host=主机地址
connection.re.port=端口
connection.re.password=密码
connection.re.index=数据库编号
```

## 使用

建议直接使用依赖注入进行。若有必要，可以使用Pool类获取：

```php
use Yesf\Connection\Pool;

$pool = Pool::getAdapter('组件名称');
echo $pool->get('key');
$pool->set('key', [1, 2, 3]);
```