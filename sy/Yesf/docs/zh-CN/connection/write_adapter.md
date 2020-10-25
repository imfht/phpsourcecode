---
title: 编写适配器
lang: zh-CN
---

# 编写适配器

实现各功能的实际内容，初始化时会传入一个`PoolInterface`：

```php
class MyAdapter {
	private $pool;

	public function __construct(PoolInterface $pool) {
		$this->pool = $pool;
	}
}
```

在Pool上注册：

```php
namespace YesfApp;

use Yesf\Connection\Pool;

class Configuration {
	public function setPool() {
		// myadapter是名称，可随意定义
		Pool::setAdapter('myadapter', MyAdapter::class);
	}
}
```

接下来，在配置中这样使用：

```ini
connection.my.adapter=myadapter
```