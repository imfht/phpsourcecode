---
title: 连接池
lang: zh-CN
---

# 连接池

Yesf支持协程连接池。整体结构如下：

* 驱动（连接池&协议实现）
* 适配器

用一段简单的代码来表示：

```php
class A_Pool {
	// connect, pool manager, etc
}

class A_Adapter implements SomeInterface {
	private $pool; // instance of A_Pool
}
```

Yesf中所有数据库连接均为连接池，这是因为一个协程进入阻塞状态时，当前进程会先处理下一个请求。当阻塞的连接响应时，再回去处理之前的协程。若一个连接在多个协程中使用，当连接响应时，Swoole可能无法唤醒正确的协程。

## 配置

全局配置：

```ini
; 最小连接数
connection.default.min=1
; 最大连接数
connection.default.max=5
```

单个配置：

```ini
; 最小连接数，可选
connection.my.min=1
; 最大连接数，可选
connection.my.max=5
; 适配器名称
connection.my.adapter=mysql
; 驱动名称
connection.my.driver=mysql
; 主机名
connection.my.host=localhost
; 端口
connection.my.port=3306
```