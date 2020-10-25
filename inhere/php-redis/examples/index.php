<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2017/3/29 0029
 * Time: 22:12
 */

require __DIR__ . '/autoload.php';

use inhere\redis\ClientFactory;
use inhere\redis\ClientInterface;

$config = [
    'host' => '127.0.0.1',
    'port' => 6379,
    'timeout' => 0.0,
    'database' => 0,
];

$client = ClientFactory::make($config);

echo "the redis client have been created.\n";

// add some event listen
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

echo $client->ping() . PHP_EOL; // +PONG

echo "test set/get value:\n";

$suc = $client->set('key0', 'val0');
$ret0 = $client->get('key0');

var_dump($suc, $ret0);

echo "test del key:\n";

$suc = $client->del('key0');
$ret0 = $client->get('key0');

var_dump($suc, $ret0);

$client->disconnect();