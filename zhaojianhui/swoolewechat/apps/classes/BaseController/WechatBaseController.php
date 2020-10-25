<?php
namespace App\BaseController;

/**
 * 微信接口基类控制器
 * @package App\Controller\Api
 */
class WechatBaseController extends BaseController
{
    /**
     * 构造函数
     * @param \Swoole $swoole
     */
    public function __construct(\Swoole $swoole)
    {
        parent::__construct($swoole);
    }
}