<?php
// +----------------------------------------------------------------------
// | RXThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2019 http://rxthink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * 系统登录-控制器
 * 
 * @author 牧羊人
 * @date 2018-12-10
 */
namespace app\admin\controller;
use app\admin\service\AdminService;
class LoginController extends AdminBaseController
{
    /**
     * 构造方法
     * 
     * @author 牧羊人
     * @date 2018-12-10
     */
    function __construct()
    {
        parent::__construct();
        $this->service = new AdminService();
    }
    
    /**
     * 登录首页
     * 
     * @author 牧羊人
     * @date 2018-12-10
     * (non-PHPdoc)
     * @see \app\admin\controller\AdminBaseController::index()
     */
    function index()
    {
        return $this->render();
    }
    
    /**
     * 系统登录
     * 
     * @author 牧羊人
     * @date 2018-12-10
     */
    function login()
    {
        if($this->request->isPost()) {
            $message = $this->service->login();
            return $message;
        }
        if($_GET['do'] == 'exit') {
            //清除登录人ID
            session('adminId',null);
            $this->redirect('/Login/index');
        }
    }
    
}