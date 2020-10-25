<?php

/**
 * WebSocket default controller
 *
 */

use PhaSvc\Base\WebSocketBase;

class MainTask extends WebSocketBase
{
    public function mainAction()
    {
        $this->parseArguments();
        $this->send('WELCOME');
    }//end


    public function closeAction()
    {
        $this->parseArguments();
        $this->ws->close(self::$fd);
    }//end


    public function taskAction()
    {
        $this->parseArguments();
        $ret = $this->ws->task(['fd' => $this->fd, 'data' => $this->params]);
        $this->send($ret??'nil');
    }//end

    public function whoamiAction()
    {
        $this->parseArguments();
        $this->send(self::$fd);
    }//end

}//end
