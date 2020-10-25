<?php


namespace Kernel\Swoole\Event\Http;

use Kernel\AgileCore;
use Kernel\Core\IComponent\IConnectionPool;
use Kernel\Swoole\Event\Event;
use Kernel\Swoole\Event\EventTrait;
use Kernel\Swoole\SwooleHttpServer;

class WorkerStart implements Event
{
        use EventTrait;
        /* @var  \swoole_http_server $server*/
        protected $server;

        public function __construct(\swoole_http_server $server)
        {
                $this->server = $server;
        }

        public function doEvent(\swoole_server $server, $workerId)
        {
                /** @var IConnectionPool $poolClass */
                AgileCore::getInstance()->get('pool');

                if(SwooleHttpServer::getAppType() === 'yaf') {
                    $config = AgileCore::getInstance()->get('config');
                    //todo: see http://php.net/manual/zh/yaf-application.construct.php
                    //It's can be array or file path(ini)
                    $config = $config->get('application');
                    SwooleHttpServer::setApplication(new \Yaf_Application($config));
                }
                $this->doClosure();
        }
}