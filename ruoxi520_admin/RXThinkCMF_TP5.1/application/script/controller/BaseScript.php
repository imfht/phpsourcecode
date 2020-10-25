<?php

namespace app\script\controller;

use think\Controller;

/**
 * 脚本基类
 * @author 牧羊人
 * @date 2019/6/25
 * Class BaseScript
 * @package app\script\controller
 */
class BaseScript extends Controller
{
    // 模型
    protected $model;
    // 服务类
    protected $service;

    /**
     * 构造方法
     * @author 牧羊人
     * @date 2019/6/25
     */
    public function initialize()
    {
        parent::initialize();
        // TODO...
    }
}
