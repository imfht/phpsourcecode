---
title: 文件
lang: zh-CN
---

# 文件

缓存至本地文件

## 配置

下列中“fs”表示组件名称，在设置默认缓存组件时使用：

```ini
; 缓存至临时目录
cache.fs.path=@TMP

; 缓存至绝对路径
cache.fs.path=/path/to/cache

; 缓存至应用目录下
cache.fs.path=@APP/tmp
```

## 使用

建议直接使用依赖注入进行。若有必要，可以使用Container获取：

```php
use Yesf\DI\Container;
use Yesf\Cache\Adapter\File;

$pool = Container::getInstance()->get(File::class);
echo $pool->get('key');
$pool->set('key', [1, 2, 3]);
```