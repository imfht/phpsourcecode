---
title: 缓存
lang: zh-CN
---

# 缓存

目前自带以下缓存支持库：

* [Redis](redis.md)
* [文件](file.md)
* [Yac](yac.md)

您也可以[自定义缓存支持库](custom.md)

## 配置默认缓存

在配置中写入：

```ini
cache.default=默认缓存组件名称
```