<?php
require dirname(__DIR__) . '/vendor/autoload.php';

$server = new Swoole\Http\Server('127.0.0.1', 9501);

$server->set([
    'dispatch_mode' =>  1, // 这里仅为演示不同 workerId 数据共享而设为 1
]);

// 添加一个用户自定义的工作进程，启动 unix socket 服务
$server->addProcess(new Swoole\Process(function($process) {
    $options = [
        // 这个文件必须，而且不能是samba共享文件
        'socketFile'    =>  '/swoole-shared-memory.sock',
        'storeTypes'    =>  [
            \Yurun\Swoole\SharedMemory\Store\KV::class,
            \Yurun\Swoole\SharedMemory\Store\Stack::class,
            \Yurun\Swoole\SharedMemory\Store\Queue::class,
            \Yurun\Swoole\SharedMemory\Store\PriorityQueue::class,
        ],
    ];
    $server = new \Yurun\Swoole\SharedMemory\Server($options);
    $server->run();
}));

$server->on('request', function (swoole_http_request $request, swoole_http_response $response) use($server) {
    $client = new \Yurun\Swoole\SharedMemory\Client\Client([
        // 这个文件必须，而且不能是samba共享文件
        'socketFile'    =>  '/swoole-shared-memory.sock',
    ]);
    $client->connect();

    $kv = new \Yurun\Swoole\SharedMemory\Client\Store\KV($client);

    switch($request->server['path_info'])
    {
        case '/set':
            $result = $kv->set($request->get['k'], $request->get['v']);
            break;
        case '/get':
            $result = $kv->get($request->get['k']);
            break;
        default:
            $result = 'are you ok?';
            break;
    }

    $response->header('Content-Type', 'application/json');
    $response->end(json_encode([
        'result'    =>  $result,
        'workerId'  =>  $server->worker_id,
    ]));

});

$server->start();

// 尝试访问：

// http://127.0.0.1:9501/set?k=a&v=123
// http://127.0.0.1:9501/get?k=a
