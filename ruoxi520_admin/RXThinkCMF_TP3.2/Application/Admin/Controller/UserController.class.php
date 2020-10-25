<?php
// +----------------------------------------------------------------------
// | RXThink框架 [ RXThink ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2019 南京RXThink工作室
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * 会员-控制器
 * 
 * @author 牧羊人
 * @date 2018-09-08
 */
namespace Admin\Controller;
use Admin\Model\UserModel;
use Admin\Service\UserService;
class UserController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new UserModel();
        $this->service = new UserService();
    }
    
    /**
     * 设置会员状态
     * 
     * @author 牧羊人
     * @date 2018-09-08
     */
    function setStatus() {
        if(IS_POST) {
            $message = $this->service->setStatus();
            $this->ajaxReturn($message);
            return ;
        }
    }
    
}