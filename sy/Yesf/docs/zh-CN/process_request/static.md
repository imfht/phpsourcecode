---
title: 静态文件
lang: zh-CN
---

# 静态文件

Yesf支持配置静态文件目录，收到Http请求会先判断是否存在此文件，如果存在会直接发送文件内容给客户端，不再进行路由解析等后续操作。

## 配置

在项目配置中：

```php
'static' => [
	'enable' => true,
	'prefix' => '/',
	'dir' => '@APP/Static'
]
// 或
'static' => true
```

* prefix: 静态文件前缀，只有此前缀的请求会尝试判断是否存在文件。默认为`/`。
* dir：静态文件目录，`@APP`代表应用目录。默认为`应用目录/Static`。

例如：

| prefix | dir | 请求URI | 文件路径 |
| ------ | --- | ------ | ------- |
| / | @APP/Static | /style.css | 应用目录/Static/style.css |
| /static | @APP/Static | /style.css | 不匹配 |
| /static | @APP/Static | /static/style.css | 应用目录/Static/style.css |