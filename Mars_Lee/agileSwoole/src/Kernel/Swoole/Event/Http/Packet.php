<?php


namespace Kernel\Swoole\Event\Http;


use Kernel\Swoole\Event\Event;
use Kernel\Swoole\Event\EventTrait;

class Packet implements Event
{
        use EventTrait;
        /* @var  \swoole_http_server $server*/
        protected $server;
        public function __construct(\swoole_http_server $server)
        {
                $this->server = $server;
        }
        public function doEvent(\swoole_server $server, string $data, array $client_info)
        {
                $this->doClosure();
                //$response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
                //$response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
        }
}