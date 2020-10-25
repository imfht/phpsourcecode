<?php

$server = new Swoole\Http\Server('127.0.0.1', 9501);

$process = new Swoole\Process(function($process) use ($server) {
        while (true) {
                $msg = $process->read();
                var_dump("process receive:", $msg);
                foreach($server->connections as $conn) {
                        $server->send($conn, $msg);
                }
        }
});

$server->addProcess($process);
$server->on('request', function (swoole_http_request $request, swoole_http_response $response) use ($process) {
        if($request->server['request_uri'] == '/favicon.ico') {
            $response->end('no');
            return;
        }

        $process->write('ok');
        $response->end('ok!');
});
$server->on('receive', function ($serv, $fd, $from_id, $data){
        var_dump($data);
        //群发收到的消息
        //$process->write($data);
});

$server->start();
