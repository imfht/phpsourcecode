<?php

namespace Kernel\Swoole;


use Kernel\Server;

class ServerProcess
{
        protected $server;
        public function __construct(Server $server)
        {
                $this->server = $server->getServer();
        }

        public function addProcess(\Closure $closure)
        {
                $process = new \swoole_process($closure);
                $this->server->addProcess($process);
        }
}