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
 * 后台登录-控制器
 * 
 * @author 牧羊人
 * @date 2018-07-`8
 */
namespace Admin\Controller;
use Admin\Service\AdminService;
class LoginController extends BaseController {
    public function __construct() {
        parent::__construct();
        $this->service = new AdminService();
        
    }
    
    /**
     * 登录入口
     * 
     * @author 牧羊人
     * @date 2018-07-18
     * (non-PHPdoc)
     * @see \Admin\Controller\BaseController::index()
     */
    function index() {
        $this->display();
    }
    
    /**
     * 用户登录
     * 
     * @author 牧羊人
     * @date 2018-06-21
     */
    public function login() {
        if(IS_POST) {
            $message = $this->service->login();
            $this->ajaxReturn($message);
            return;
        }
        if($_GET['do'] == 'exit'){
            unset($_SESSION['adminId']);
            $this->redirect('/Admin/Login/index');
        }
        
    }
    
    /**
     * 验证码
     *
     * @author 牧羊人
     * @date 2018-06-21
     */
    public function verify() {
        $conf = array(
            //'useZh'=>true,//使用中文
            'fontSize'=>14,
            'length'=>4,
            'imageW'=>95,//验证码宽度
            'imageH'=>33,//验证码
            'useNoise'=>true,//是否添加杂点
            //'codeSet'=>'0123456789',
        );
        $Verify = new \Think\Verify($conf);
        $Verify->entry();
    }
    
    /**
     * 验证码校验（备用）
     *
     * @author 牧羊人
     * @date 2018-07-06
     */
    public function check_verify($code, $id = '')
    {
        $verify = new \Think\Verify();
        $res = $verify->check($code, $id);
        $this->ajaxReturn($res, 'json');
    }
    
}