# SphinxClient-Swoole

[![Latest Version](https://img.shields.io/packagist/v/yurunsoft/sphinx-client-swoole.svg)](https://packagist.org/packages/yurunsoft/sphinx-client-swoole)
[![Php Version](https://img.shields.io/badge/php-%3E=7.0-brightgreen.svg)](https://secure.php.net/)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=4.0.3-brightgreen.svg)](https://github.com/swoole/swoole-src)
[![IMI License](https://img.shields.io/github/license/Yurunsoft/SphinxClient-Swoole.svg)](https://github.com/Yurunsoft/SphinxClient-Swoole/blob/master/LICENSE)

## 介绍

这是一个适合用于 Swoole 协程环境下的 SphinxClient，支持内置连接池。

代码基于 Sphinx 官方自带的 SphinxClient 修改，完全遵守原项目的 GPL-2.0 协议。

## 使用说明

Composer:`"yurunsoft/sphinx-client-swoole":"~1.0"`

使用方式和 Sphinx 官方自带的 SphinxClient 并无两样，唯一需要注意的是只支持在 Swoole 协程下运行。

### 连接池

```php
// 初始化连接池，改为你自己的连接配置
SphinxPool::init(5, '192.168.0.110', 9312);
// 连接池使用
SphinxPool::use(function($sphinxClient){
	// 改成你自己的搜索名和索引名
	$result = $sphinxClient->Query('query string', 'indexName');
		
	if($result)
	{
		var_dump($result['total']);
	}
	else
	{
		var_dump($sphinxClient->GetLastError());
	}
});
```

### 直接实例化

```php
$client = new SphinxClient;
// 改为你自己的连接配置
$client->SetServer('192.168.0.110', 9312);
// 改成你自己的搜索名和索引名
var_dump($client->Query('query string', 'indexName'));
```

更加详细的示例代码请看`test`目录下代码。