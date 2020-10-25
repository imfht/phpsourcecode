<?php
// +----------------------------------------------------------------------
// | RXThinkCMF框架 [ RXThinkCMF ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2020 南京RXThinkCMF研发中心
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <1175401194@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;


use app\admin\service\AdminRomService;
use app\common\controller\Backend;

/**
 * 权限控制器
 * Class Adminrom
 * @package app\admin\controller
 */
class Adminrom extends Backend
{
    /**
     * 初始化方法
     * @author 牧羊人
     * @date 2019/5/29
     */
    public function initialize()
    {
        parent::initialize();
        $this->service = new AdminRomService();
    }

    /**
     * 获取角色权限
     * @return mixed
     */
    public function index()
    {
        $result = $this->service->getList();
        return $result;
    }

    /**
     * 设置角色权限
     * @return mixed
     */
    public function setPermission()
    {
        $result = $this->service->setPermission();
        return $result;
    }
}