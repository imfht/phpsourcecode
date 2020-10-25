---
title: 编写驱动
lang: zh-CN
---

# 编写驱动

实现`Yesf\Connection\PoolInterface`接口：

```php
interface PoolInterface {
	/**
	 * Setup connection pool
	 * 
	 * @access public
	 * @param array $config
	 */
	public function initPool($config);
	/**
	 * Get a connection from pool
	 * 
	 * @access public
	 * @return object
	 */
	public function getConnection();
	/**
	 * Put a connection into pool
	 * 
	 * @access public
	 * @param object $connection
	 */
	public function freeConnection($connection);
	/**
	 * Close a connection
	 * 
	 * @access public
	 */
	public function close();
	/**
	 * Re-connect
	 * 
	 * @access public
	 * @param object $connection
	 */
	public function reconnect($connection);
}
```

你可以使用`Yesf\Connection\PoolTrait`来减少你的编码量，你只需实现`reconnect`，例如：


```php
class MyDriver implements PoolInterface {
	use PoolTrait;
	protected $config = null;
	public function __construct(array $config) {
		$this->config = $config;
		$this->initPool($config);
	}
	public function reconnect($connection) {
		$dsn = 'mysql:host=' . $this->config['host'] . ';port=' . $this->config['port'] . ';';
		if (isset($this->config['database'])) {
			$dsn .= 'dbname=' . $this->config['database'] . ';';
		}
		try {
			$handle = new \PDO($dsn, $this->config['user'], $this->config['password']);
		} catch (\PDOException $e) {
			throw new DBException($e->getMessage());
		}
		return $handle;
	}
}
```

接下来，在Pool上注册：

```php
namespace YesfApp;

use Yesf\Connection\Pool;

class Configuration {
	public function setPool() {
		// mydriver是名称，可随意定义
		Pool::setDriver('mydriver', MyDriver::class);
	}
}
```

接下来，在配置中这样使用：

```ini
; 连接驱动
connection.my.driver=mydriver
; 其他连接需要的配置
connection.name.host=localhost
connection.name.port=9000
```