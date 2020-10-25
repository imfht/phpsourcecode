<?php
/**
 * Class MainTask
 *
 * @description('Welcome', '显示欢迎信息')
 */

use PhaSvc\Base\TaskBase;

class MainTask extends TaskBase
{
    /**
     * Main
     *
     * @description('显示系统消息')
     */
    public function mainAction()
    {
        return $this->dispatcher->forward([
            "controller" => "info",
            "action"     => "main",
        ]);
    }//end


}//end
