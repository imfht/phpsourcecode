# php-redis

简单的redis操作客户端包装库

- 使用方便，直接访问所有 `\Redis` 的所有命令
- 支持配置 单例模式, 主从模式， 集群模式
- 支持连接，操作事件监听
- 主从模式时，会自动选择 reader/writer 来执行对应的命令

[EN README](./README.md)

## 项目

- **github** https://github.com/inhere/php-redis.git
- **git@osc** https://git.oschina.net/inhere/php-redis.git

## 安装

> NOTICE: 依赖php的 'redis' 扩展

- use composer

编辑 `composer.json`, 添加

```
"inhere/redis": "dev-master",
```

然后运行: `composer update`

- 直接拉取

```
git clone https://github.com/inhere/php-redis.git // github
git clone https://git.oschina.net/inhere/php-redis.git // git@osc
```

## 配置

### 单例配置

```php
$config = [
    'host' => '127.0.0.1',
    'port' => 6379,
    'timeout' => 0.0,
    'database' => 0,
];
```

### 主从配置

```php
$config = [
      'mode' => 2, // 1 singleton 2 master-slave 3 cluster
      'master' => [
          'host' => '127.0.0.1',
          'port' => 6379,
          'timeout' => 0.0,
          'database' => 0,
      ],
      'slaves' => [
          'slave0' => [
              'host' => '127.0.0.1',
              'port' => 6380,
              'timeout' => 0.0,
              'database' => 0,
          ]
      ],
    ];
```

### 集群配置

```php
$config = [
     'mode' => 3, // 1 singleton 2 master-slave 3 cluster
     'name1' => [
         'host' => '127.0.0.1',
         'port' => '6379',
         'database' => '0',
         'options' => []
     ],
     'name2' => [
         'host' => '127.0.0.2',
         'port' => '6379',
         'database' => '0',
         'options' => []
     ],
];
```

## 创建客户端

根据不同的配置会自动创建对应的客户端实例

> 创建时不会进行连接，当发生命令操作时，才会进行连接

```php
use inhere\redis\ClientFactory;

$client = ClientFactory::make($config);
```

## 事件监听

支持四个事件 `连接时` `断开连接时` `执行命令之前` `执行命令之后`, 方便进行调试和记录操作日志

```php
    // ARGS: ($name, $mode, $config)
    const CONNECT = 'connect';
    // ARGS: ($name, $mode)
    const DISCONNECT = 'disconnect';

    // ARGS: ($method, array $args, $operate)
    const BEFORE_EXECUTE = 'beforeExecute';

    // ARGS: ($method, array $data, $operate)
    const AFTER_EXECUTE = 'afterExecute';
```

### 添加事件监听

```php
// 连接时
$client->on(ClientInterface::CONNECT, function($name, $mode, $config) {
    printf("CONNECT:connect to the name=%s,mode=%s,config=%s\n", $name, $mode, json_encode($config));
});

// 断开连接时
$client->on(ClientInterface::DISCONNECT, function($name, $mode) {
    $names = 'all';

    if ($name) {
        $names = is_array($name) ? implode(',', $name) : $name;
    }

    printf("DISCONNECT:close there are %s connections,mode=%s\n", $names, $mode);
});

// 执行命令之前
$client->on('beforeExecute', function ($cmd, array $args, $operate)
{
    printf("BEFORE_EXECUTE:will be execute the command=$cmd, operate=$operate, args=%s\n", json_encode($args));
});

// 执行命令之后
$client->on('afterExecute', function ($cmd, array $data, $operate)
{
    printf("AFTER_EXECUTE:has been executed the command=$cmd, operate=$operate, data=%s\n", json_encode($data));
});
```

## 使用

```php
echo $client->ping(); // +PONG

echo "test set/get value:\n";

$suc = $client->set('key0', 'val0'); // bool(true)
$ret0 = $client->get('key0'); // string(4) "val0"

var_dump($suc, $ret0);

echo "test del key:\n";

$suc = $client->del('key0'); // int(1)
$ret0 = $client->get('key0'); // bool(false)

var_dump($suc, $ret0);
```

更多请看示例 [examples](./examples)

## License

MIT

## 我的其他项目

### `inhere/console` [github](https://github.com/inhere/php-console) [git@osc](https://git.oschina.net/inhere/php-console)

功能丰富的命令行应用，命令行工具库

### `inhere/sroute` [github](https://github.com/inhere/php-srouter)  [git@osc](https://git.oschina.net/inhere/php-srouter)
 
 轻量且功能丰富快速的路由库

### `inhere/php-validate` [github](https://github.com/inhere/php-validate)  [git@osc](https://git.oschina.net/inhere/php-validate)
 
 一个简洁小巧且功能完善的php验证库。仅有几个文件，无依赖。
 
### `inhere/http` [github](https://github.com/inhere/php-http) [git@osc](https://git.oschina.net/inhere/php-http)

http 工具库(`request` 请求 `response` 响应 `curl` curl请求库，有简洁、完整和并发请求三个版本的类)