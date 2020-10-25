<?php

namespace App\BaseController;

use Swoole;

/**
 * 接口基类控制器.
 * @property \EasyWeChat\Foundation\Application $easywechat
 */
class BaseController extends Swoole\Controller
{
    /**
     * 构造函数.
     *
     * @param Swoole $swoole
     */
    public function __construct(\Swoole $swoole)
    {
        parent::__construct($swoole);
        //判断是否ajax请求
        if(isset($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            $this->is_ajax = true;
        }
    }
}
