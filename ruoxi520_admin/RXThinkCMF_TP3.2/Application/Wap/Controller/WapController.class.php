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
 * WAP站基类控制器
 * 
 * @author 牧羊人
 * @date 2018-09-30
 */
namespace Wap\Controller;
use Common\Controller\BaseController;
use Wap\Model\UserModel;
class WapController extends BaseController {
    public $mod,$service,$userId,$userInfo,$openid;
    function __construct() {
        parent::__construct();
        
        //初始化系统配置
        $this->initConfig();
        
//         //初始化数据
//         $this->initSession();
        
//         //初始化数据
//         $this->checkLogin();
        
    }
    
    /**
     * 初始化方法
     * 
     * @author 牧羊人
     * @date 2018-09-30
     * (non-PHPdoc)
     * @see \Common\Controller\BaseController::_initialize()
     */
    public function _initialize() {
        parent::_initialize();
        
        //添加不需要登录验证的链接,全部小写
        $not_need_login = [
            'Index/index',
        ];
        
        //链接转小写,以便兼容URL大小写不同一的问题
        $action = strtolower(trim(__ACTION__,'/'));
        if(!in_array($action, $not_need_login)) {
            //检查是否登录
            if(!check_login()) {
                if(IS_AJAX) {
                    $this->ajaxReturn(message('您需要登录！',fasle));
                }else{
                    $this->error("您需要登录！");
                }
            }
        }
        
    }
    
    /**
     * 初始化系统配置
     *
     * @author 牧羊人
     * @date 2018-09-27
     */
    private function initConfig() {
    
        //设置基础参数
        $this->assign("title", "证件国际");
        $this->assign("mUrl", WAP_URL);
        $this->assign('resUrl',WAP_URL . "/Public/WAP");
    
        //系统应用参数
        define('ROOT_PATH', realpath(__ROOT__));
        define('APP', CONTROLLER_NAME);
        define('ACT', ACTION_NAME );
        $this->assign('mdu' , __MODULE__);
        $this->assign('app' , APP);
        $this->assign('act' , ACT);
    
    }
    
    /**
     * 初始化数据
     *
     * @author 牧羊人
     * @date 2018-03-29
     */
    function initSession() {
    
//         //获取OPENID
//         $weixinService = new WeixinService();
//         $openid = $weixinService->getWeixinOpenId();
//         $this->openid = $openid;
    
    }
    
    /**
     * 检查登录状态
     *
     * @author 牧羊人
     * @date 2018-09-28
     */
    function checkLogin() {
    
        if(!$this->openid) {
            exit("微信授权失败");
        }
    
        //注册验证
        $userMod = new UserModel();
        $userInfo = $userMod->getRowByAttr([
            'openid'=>$this->openid,
        ]);
        if(!$userInfo) {
            $userId = $userMod->edit([
                'openid'=>$this->openid,
            ]);
        }else{
            $userId = $userInfo['id'];
        }
    
        //设置用户信息
        $_SESSION['userId'] = $userId;
        $this->userId = $userId;
        $userInfo = $userMod->getInfo($userId);
        $_SESSION['userInfo'] = $userInfo;
        $this->userInfo = $userInfo;
    
    }
    
    /**
     * 模板渲染
     *
     * @author 牧羊人
     * @date 2018-07-11
     */
    public function render($tpl="", $data=array()) {
        if (empty($tpl)) {
            $tpl = lcfirst(APP) . "." . ACT;
        }else if(strpos($tpl, ".html")>0){
            $tpl = rtrim($tpl, ".html");
        }
        if ($data) {
            foreach ($data as $name=>$value) {
                $this->assign($name, $value);
            }
        }
        //渲染头部
        $this->display("Public:header");
        //渲染主体
        if (strpos($tpl, "/")===0) {
            $tpl = ltrim($tpl, "/");
        }
        $this->display(APP.":{$tpl}");
        //渲染底部
        $this->display("Public:footer");
    }
    
    /**
     * 输出JSON数据
     *
     * @author 牧羊人
     * @date 2018-09-27
     */
    public function jsonReturn() {
        $arr = func_get_args();
        if (!is_array($arr[0])) {
            $result = call_user_func_array("message", $arr);
        } else {
            $result = $arr[0];
        }
        echo json_encode($result);
        exit();
    }
    
}