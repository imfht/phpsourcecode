<?php


namespace Kernel\Swoole\Event;


interface Event
{
        public function setEventCall(\Closure $closure = null, array $params = []);
}