<?php
namespace app\common;

use Channel\Server;

class ChannelServer extends Server
{

    /**
     * Construct.
     * @param string $ip
     * @param int $port
     */
    public function __construct($ip = '0.0.0.0', $port = 2206)
    {
        $worker = new Worker("frame://$ip:$port");
        $worker->count = 1;
        $worker->name = 'ChannelServer';
        $worker->channels = array();
        $worker->onMessage = array($this, 'onMessage') ;
        $worker->onClose = array($this, 'onClose'); 
        $this->_worker = $worker;
    }

    public function getWorker()
    {
        return $this->_worker;
    }
}
