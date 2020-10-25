<?php
namespace app\exwechat\controller;

use youwen\exwechat\exRequest;

/**
 * 微信事件消息－控制器
 *
 */
class HandleTest extends AbstractHandle
{

    // private $msg;
    public function handle($reylyContext='')
    {
        // $this->msg = empty($arrayMsg) ? exRequest::instance()->getMsg() : $arrayMsg;
        
        $this->response($reylyContext);

        exit; //阻止DEBUG信息输出
    }

    public function setMyScene($openId='123', $value='haha')
    {
        $ret = $this->setScene($openId, $value);
        echo '<pre>';
        print_r( $ret );
        exit('</pre>');
    }

    public function myScene($openId='')
    {
        $ret = $this->getScene($openId);
        echo '<pre>';
        print_r( $ret );
        exit('</pre>');
    }
}
