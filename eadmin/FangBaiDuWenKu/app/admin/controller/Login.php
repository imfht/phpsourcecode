<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\controller\ControllerBase;
use app\admin\logic\Login as LogicLogin;


use think\Config;
/**
 * 登录控制器
 */
class Login extends ControllerBase
{
    
    // 登录逻辑
    private static $loginLogic = null;
   
   
    /**
     * 构造方法
     */
    public function _initialize()
    {
        // 执行父类构造方法
        parent::_initialize();
        
        self::$loginLogic = get_sington_object('loginLogic', LogicLogin::class);
      
        
        
    }
  
    /**
     * 登录
     */
    public function login()
    {
    	
        is_login() && $this->jump(RESULT_REDIRECT, 'Index/adminindex');
       $yzm_list = parse_config_array('yzm_list');//1\注册2\登录3\忘记密码4\后台登录
        if(in_array(4, $yzm_list)){
        	$yzm=1;
        }else{
        	$yzm=0;
        }
  
        $this->assign('yzm',$yzm);
       
        
        return $this->fetch('login');
    }
    
    /**
     * 登录处理
     */
    public function loginHandle($username = '', $password = '', $verify = '')
    {
    	
    	
    	
    	
    $this->jump(self::$loginLogic->loginHandle($username, $password, $verify));
    }
    /**
     * 登录处理
     */
    public function locker($username,$password)
    {
    	 
    	 
    	 
    	 
    	$this->jump(self::$loginLogic->locker($username, $password));
    }
    /**
     * 注销登录
     */
    public function logout()
    {
        
        $this->jump(self::$loginLogic->logout());
    }
    /**
     * 清理缓存
     */
    public function clearCache()
    {
    
    	$this->jump(self::$loginLogic->clearCache());
    }
}
