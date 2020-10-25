<?php
namespace app\exwechat\controller;

use youwen\exwechat\exRequest;

/**
 * 微信事件消息－控制器
 *
 */
class HandleDefault extends AbstractHandle
{

    // private $msg;
    public function handle($reylyContext='')
    {
        // $this->msg = empty($arrayMsg) ? exRequest::instance()->getMsg() : $arrayMsg;
        
        $this->response($reylyContext);

        exit; //阻止DEBUG信息输出
    }
}
