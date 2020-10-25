<?php


namespace Kernel;


interface Server
{
        public function start() : Server;
        public function shutdown(\Closure $callback = null) : Server;
        public function getServer() : \swoole_server;
        public function setTask(string $event, \Closure $closure) : Server;
}