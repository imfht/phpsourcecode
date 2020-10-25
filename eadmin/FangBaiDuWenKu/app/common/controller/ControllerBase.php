<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\controller;

use think\Controller;

/**
 * 系统通用控制器基类
 */
class ControllerBase extends Controller
{
    
    // 请求参数
    protected $param;
    protected $captcha;
    /**
     * 基类初始化
     */
    protected function _initialize()
    {
        
        // 初始化请求信息
        $this->initRequestInfo();

        $this->captcha = new \think\captcha\Captcha(config('captcha'));
       
        
     timeouturl(url('admin/Ybcommand/twentyfour'),24*60*60,'twentyfour');
    }
    
    /**
     * 初始化请求信息
     */
    final private function initRequestInfo()
    {
        
        defined('IS_POST')          or define('IS_POST',         $this->request->isPost());
        defined('IS_GET')           or define('IS_GET',          $this->request->isGet());
        defined('MODULE_NAME')      or define('MODULE_NAME',     $this->request->module());
        defined('CONTROLLER_NAME')  or define('CONTROLLER_NAME', $this->request->controller());
        defined('ACTION_NAME')      or define('ACTION_NAME',     $this->request->action());
        defined('URL')              or define('URL',             strtolower($this->request->controller() . SYS_DSS . $this->request->action()));
        defined('URL_MODULE')       or define('URL_MODULE',      strtolower($this->request->module()) . SYS_DSS . URL);
        defined('CLIENT_IP')       or define('CLIENT_IP',      $this->request->ip());
        $this->param = $this->request->param();
    }
    
    /**
     * 系统通用跳转
     */
    final protected function jump($jump_type = null, $message = null, $url = null,$data = null)
    {
        
        if (is_array($jump_type)):
            
        switch (count($jump_type))
        {
            case 2  : list($jump_type, $message)       = $jump_type; break;
            case 3  : list($jump_type, $message, $url) = $jump_type; break;
            case 4  : list($jump_type, $message, $url,$data) = $jump_type; break;
            default : die(RESULT_ERROR);
        }
        
        endif;
        
        $success  = RESULT_SUCCESS;
        $error    = RESULT_ERROR;
        $redirect = RESULT_REDIRECT;

        switch ($jump_type)
        {
            case $success  : $this->success($message, $url,$data); break;
            case $error    : $this->error($message, $url,$data);   break;
            case $redirect : $this->$redirect($message);      break;
            default        : die(RESULT_ERROR);
        }
    }
    /**
     * 验证码调用
     */
    public function captchaShow()
    {
    
    	
    	return $this->captcha->entry();
    	
    	
    }
    /**
     * 验证码验证
     */
    public function captchaCheck($code)
    {
    	 
    	 
    	return $this->captcha->check($code);
    	 
    	 
    }
}
