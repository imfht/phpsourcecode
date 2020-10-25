<?php
namespace app\exwechat\controller;

use youwen\exwechat\exRequest;

/**
 * 微信事件消息－控制器
 *
 */
class HandleScene extends AbstractHandle
{

    // private $msg;
    public function handle($sceneRet='')
    {
        return $this->chat($sceneRet);
    }

    public function chat($sceneRet)
    {
        if($sceneRet['sceneValue'] == 'aa'){
            $this->response('AA scene');
        }
        if($sceneRet['sceneValue'] == 'bb'){
            $map = ['openId'=>$sceneRet['openId'], 'sceneType'=>$sceneRet['sceneType']];
            db('we_scene')->where($map)->update(['sceneValue'=>123]);
            $this->response('AA scene');
        }
        return true;
    }

}
