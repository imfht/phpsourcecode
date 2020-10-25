<?php

namespace App\Tasks\Registry;

use App\Core\Cli\Task\Socket;
use App\Core\Registry\Exceptions\RegistryException;
use App\Core\Registry\Input;
use App\Core\Registry\Persistent\Redis;
use swoole_server;

class ServerTask extends Socket
{
    // 端口号
    protected $port = 11521;

    protected $config = [
        'pid_file' => ROOT_PATH . '/socket.pid',
        'daemonize' => false,
        'max_request' => 500, // 每个worker进程最大处理请求次数
    ];

    protected $services = [];

    protected $length = 10;

    protected function events()
    {
        return [
            'receive' => [$this, 'receive'],
            'WorkerStart' => [$this, 'workerStart'],
            'WorkerStop' => [$this, 'workerStop'],
        ];
    }

    public function workerStop(swoole_server $serv, $workerId)
    {
        // 进程结束时，可以持久化当前的服务列表
        if (env('REGISTRY_PERSISTENT', false)) {
            $client = Redis::getInstance();
            $services = json_encode($this->services);
            $client->set('phalcon:registry:service:persistent', $services);
        }
    }

    public function workerStart(swoole_server $serv, $workerId)
    {
        // dump(get_included_files()); // 查看不能被平滑重启的文件

        // 进程开始时，如果存在持久化数据，则取到内存中
        if (env('REGISTRY_PERSISTENT', false)) {
            $client = Redis::getInstance();
            $services = $client->get('phalcon:registry:service:persistent');
            if ($services = json_decode($services, true)) {
                $this->services = $services;
            }
        }

    }

    /**
     * @desc
     * @author limx
     * @param swoole_server $server
     * @param int           $fd
     * @param int           $reactor_id
     * @param string        $data
     *
     * input = {
     *     service:xxx,
     *     ip:xxx,
     *     port:xxx,
     *     nonce:xxx,
     *     sign:xxxx,
     *     register:false,
     * }
     *
     * output = {
     *     success:true,
     *     message:"",
     *     services:[{
     *         service:xxx,
     *         ip:xxx,
     *         port:xxx,
     *         weight:xxx
     *     },...],
     * }
     */
    public function receive(swoole_server $server, $fd, $reactor_id, $data)
    {
        $success = true;
        $message = '';

        try {
            if ($data = json_decode($data, true)) {
                $service = new Input($data);
                // 把元素加入到services表相应服务首位
                if ($service->register) {
                    $key = $service->service;
                    $this->services[$key] = $service->toArray();
                }
            } else {
                throw new RegistryException("The data is invalid!");
            }
        } catch (\Exception $ex) {
            $success = false;
            $message = $ex->getMessage();
        }

        $server->send($fd, json_encode([
            'success' => $success,
            'message' => $message,
            'services' => $this->services,
        ]));
    }

}

