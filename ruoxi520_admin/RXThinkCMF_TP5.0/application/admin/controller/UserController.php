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
 * 用户-控制器
 * 
 * @author 牧羊人
 * @date 2018-12-14
 */
namespace app\admin\controller;
use app\admin\model\UserModel;
use app\admin\service\UserService;
class UserController extends AdminBaseController
{
    /**
     * 构造方法
     * 
     * @author 牧羊人
     * @date 2018-12-14
     */
    function __construct()
    {
        parent::__construct();
        $this->model = new UserModel();
        $this->service = new UserService();
    }
    
    /**
     * 设置会员状态
     * 
     * @author 牧羊人
     * @date 2018-12-14
     */
    function setStatus()
    {
        if(IS_POST) {
            $message = $this->service->setStatus();
            return $message;
        }
    }
    
}