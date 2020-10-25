<?php


namespace Kernel\Swoole\Event\Http;


use Kernel\AgileCore as Core;
use Kernel\Swoole\Event\Event;
use Kernel\Swoole\Event\EventTrait;
use Library\Crawler\Crawler;
use Core\Cache\Type\Hash;

class Task implements Event
{
        use EventTrait;
        /* @var  \swoole_http_server $server*/
        protected $server;
        protected $db;
        protected $redis;
        protected $config;
        protected $data;
        const KEY = 'crawler:list:';
        const BASE_NUM = 1000;

        public function __construct(\swoole_http_server $server)
        {
                $this->server = $server;
        }

        public function doEvent(\swoole_server $server, $taskId, $fromId, $data)
        {
                $data = json_decode($data, true);

                if(!is_array($data)) {
                        return;
                }

                if(!isset($data['obj']) or !isset($data['method'])) {
                        return;
                }

                $args = $data['args'] ?? [];

                return call_user_func_array([$data['obj'], $data['method']], $args);
        }


}