<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\Web\Controllers;
use Tang\ThirdParty\ThirdPartyService;
ThirdPartyService::import('JsonRpc.jsonRPC2Server');
/**
 * JsonRpc控制器
 * Class JsonRpcController
 * @package Tang\Web\Controllers
 */
class JsonRpcController extends WebController
{
    protected $rpc;
    public function __construct()
    {
        $this->rpc = new \jsonRPCServer();
        $this->rpc->registerClass($this);
    }
    /**
     * @see Controller::invoke
     */
    protected function invoke($action)
    {
        $this->registerClass();
        $this->rpc->handle();
    }

    /**
     * 提供给子类注册对象到rpc
     */
    protected function registerClass()
    {
    }
    public function __call($method,$args)
    {
        $this->rpc->error('not found function '.$method);
        $this->rpc->sendResponse();
    }
}