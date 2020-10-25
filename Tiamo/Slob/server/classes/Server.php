<?php
namespace Server;

use Swoole;

class Server extends Swoole\Object
{
    protected $pid_file;
    /**
     * @var \swoole_server
     */
    protected $serv;

    function setLogger($log)
    {
        $this->log = $log;
    }

    function log($msg)
    {
        $this->log->info($msg);
    }
}