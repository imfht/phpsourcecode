# php-redis

a simple redis client library of the php

[中文README](./README_zh.md)

## project

- **github** https://github.com/inhere/php-redis.git
- **git@osc** https://git.oschina.net/inhere/php-redis.git

## Install

> NOTICE: php extension 'redis' is required 

- use composer

edit `composer.json`, at _require_ add

```
"inhere/redis": "dev-master",
```

run: `composer update`

- Direct fetch

```
git clone https://github.com/inhere/php-redis.git // github
git clone https://git.oschina.net/inhere/php-redis.git // git@osc
```

## config

### singleton config

```php
$config = [
    'host' => '127.0.0.1',
    'port' => 6379,
    'timeout' => 0.0,
    'database' => 0,
];
```

### master-slave config

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

### cluster config

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

## create client 

```php
use inhere\redis\ClientFactory;

// $app is my application instance.

$client = ClientFactory::make($config);
```

## add event listen

```php
$client->on(ClientInterface::CONNECT, function($name, $mode, $config) {
    printf("CONNECT:connect to the name=%s,mode=%s,config=%s\n", $name, $mode, json_encode($config));
});

$client->on(ClientInterface::DISCONNECT, function($name, $mode) {
    $names = 'all';

    if ($name) {
        $names = is_array($name) ? implode(',', $name) : $name;
    }

    printf("DISCONNECT:close there are %s connections,mode=%s\n", $names, $mode);
});

$client->on('beforeExecute', function ($cmd, array $args, $operate)
{
    printf("BEFORE_EXECUTE:will be execute the command=$cmd, operate=$operate, args=%s\n", json_encode($args));
});

$client->on('afterExecute', function ($cmd, array $data, $operate)
{
    printf("AFTER_EXECUTE:has been executed the command=$cmd, operate=$operate, data=%s\n", json_encode($data));
});
```

## Usage

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

## More

examples please the [examples](./examples)

## License

MIT
