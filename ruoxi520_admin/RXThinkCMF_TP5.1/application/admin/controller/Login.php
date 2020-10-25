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

use app\admin\service\LoginService;
use app\common\controller\Backend;

/**
 * 系统登录-控制器
 * @author 牧羊人
 * @since 2020/7/11
 * Class Login
 * @package app\admin\controller
 */
class Login extends Backend
{
    /**
     * 初始化
     * @author 牧羊人
     * @since 2020/7/11
     */
    public function initialize()
    {
        parent::initialize();
        $this->service = new LoginService();
    }

    /**
     * 登录页
     * @return mixed
     * @since 2020/7/11
     * @author 牧羊人
     */
    public function index()
    {
        // 取消模板布局
        $this->view->engine->layout(false);
        return $this->fetch();
    }

    /**
     * 系统登录
     * @return mixed
     * @since 2020/7/11
     * @author 牧羊人
     */
    public function login()
    {
        if (IS_POST) {
            $result = $this->service->login();
            return $result;
        }
    }

    /**
     * 退出登录
     * @author 牧羊人
     * @since 2020/7/11
     */
    public function logout()
    {
        // 清除SESSION值
        session('adminId', null);
        $this->redirect('/login/index');
    }
}
