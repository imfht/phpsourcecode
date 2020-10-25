<?php

namespace App\Tasks\Registry;

use App\Tasks\Task;
use App\Utils\Registry\Sign;
use limx\Support\Str;
use Xin\Cli\Color;
use swoole_client;

class ClientTask extends Task
{
    // 端口号
    protected $port = 11521;
    // 注册中心IP
    protected $ip = '127.0.0.1';

    public function mainAction()
    {
        echo Color::head('Help:'), PHP_EOL;
        echo Color::colorize('  服务注册中心测试'), PHP_EOL, PHP_EOL;

        echo Color::head('Usage:'), PHP_EOL;
        echo Color::colorize('  php run registry:client@[action]', Color::FG_GREEN), PHP_EOL, PHP_EOL;

        echo Color::head('Actions:'), PHP_EOL;
        echo Color::colorize('  heartbeat           心跳测试', Color::FG_GREEN), PHP_EOL;
    }

    public function heartbeatAction()
    {
        $client = new swoole_client(SWOOLE_SOCK_TCP);
        if (!$client->connect($this->ip, $this->port, -1)) {
            exit("connect failed. Error: {$client->errCode}\n");
        }

        $data = [
            'service' => 'test2',
            'ip' => '127.0.0.1',
            'port' => 11111,
            'nonce' => Str::random(16),
            'register' => true,
        ];
        $data['sign'] = Sign::sign($data);
        $client->send(json_encode($data));
        $result = $client->recv();
        $client->close();

        dump(json_decode($result, true));
    }

}

