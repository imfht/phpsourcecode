<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Main\Base\BaseController;
use App\Common\Code;
use App\Common\Msg;
use Lib\Wechat\WeChat;

class IndexController extends BaseController
{
    /**
     * 设置不需要权限
     */
    protected $isNoAuth = true;

    /**
     * 设置不需要登录的的Action,不加Action前缀
     * @author Chengcheng
     * @date   2016年10月23日 20:39:25
     * @return array
     */
    protected function noLogin()
    {
        // 以下Action不需要登录权限
        return ['index'];
    }

    /**
     * index
     * @author Chengcheng
     * @date 2016-10-21 09:00:00
     * @return string
     */
    function index()
    {
        $app = WeChat::app();
        var_dump(config('wechat'));die;


        $result["code"] = Code::SYSTEM_OK;
        $result["msg"]  = Msg::SYSTEM_OK;
        return $this->ajaxReturn($result);
    }

}
