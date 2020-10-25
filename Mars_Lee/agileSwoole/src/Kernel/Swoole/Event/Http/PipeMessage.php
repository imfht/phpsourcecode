<?php


namespace Kernel\Swoole\Event\Http;


use Kernel\Swoole\Event\Event;
use Kernel\Swoole\Event\EventTrait;

class PipeMessage implements Event
{
        use EventTrait;
        /* @var  \swoole_http_server $server*/
        protected $server;
        public function __construct(\swoole_http_server $server)
        {
                $this->server = $server;
        }
        public function doEvent(\swoole_server $server, $src_worker_id, $data)
        {
                echo "#{$server->worker_id} message from #$src_worker_id: $data\n";

                $this->doClosure();
        }
}