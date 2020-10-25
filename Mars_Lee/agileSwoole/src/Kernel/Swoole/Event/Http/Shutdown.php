<?php


namespace Kernel\Swoole\Event\Http;

use Kernel\Swoole\Event\Event;
use Kernel\Swoole\Event\EventTrait;

class Shutdown implements Event
{
        use EventTrait;
        protected $server;
        public function __construct(\swoole_http_server $server)
        {
                $this->server = $server;
        }

        public function doEvent()
        {
                $this->doClosure();
        }
}