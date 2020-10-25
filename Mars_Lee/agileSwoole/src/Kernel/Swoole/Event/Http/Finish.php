<?php


namespace Kernel\Swoole\Event\Http;


use Kernel\Swoole\Event\Event;
use Kernel\Swoole\Event\EventTrait;

class Finish implements Event
{
        use EventTrait;
        /* @var  \swoole_http_server $server*/
        protected $server;
        public function __construct(\swoole_http_server $server)
        {
                $this->server = $server;
        }

        public function doEvent(\swoole_server $server, $task_id, $data)
        {
                echo "Finish: $task_id say :$data".PHP_EOL;
	        $this->doClosure();
        }
}