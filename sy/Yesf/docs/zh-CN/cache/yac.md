---
title: Yac
lang: zh-CN
---

# Yac

使用Yac缓存（需安装[Yac](https://github.com/laruence/yac)）

## 配置

下列中“yac”表示组件名称，在设置默认缓存组件时使用：

```ini
; 前缀，可为空
cache.yac.prefix=app
```

## 使用

建议直接使用依赖注入进行。若有必要，可以使用Container获取：

```php
use Yesf\DI\Container;
use Yesf\Cache\Adapter\Yac;

$pool = Container::getInstance()->get(Yac::class);
echo $pool->get('key');
$pool->set('key', [1, 2, 3]);
```