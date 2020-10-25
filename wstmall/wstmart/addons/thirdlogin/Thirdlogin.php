<?php
namespace addons\thirdlogin;  // 注意命名空间规范


use think\addons\Addons;
use addons\thirdlogin\model\Thirdlogin as DM;

/**
 * WSTMart 第三方登录插件
 * @author WSTMart
 */
class Thirdlogin extends Addons{
    // 该插件的基础信息
    public $info = [
        'name' => 'Thirdlogin',   // 插件标识
        'title' => '第三方登录插件',  // 插件名称
        'description' => '集成了主流QQ,微信登录',    // 插件简介
        'status' => 0,  // 状态
        'author' => 'WSTMart',
        'version' => '1.0.1'
    ];

	
    /**
     * 插件安装方法
     * @return bool
     */
    public function install(){
    	$m = new DM();
    	$flag = $m->install();
    	WSTClearHookCache();
    	cache('hooks',null);
        return $flag;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall(){
    	$m = new DM();
    	$flag = $m->uninstall();
    	WSTClearHookCache();
    	cache('hooks',null);
        return $flag;
    }
    
	/**
     * 插件启用方法
     * @return bool
     */
    public function enable(){
    	WSTClearHookCache();
    	cache('hooks',null);
        return true;
    }
    
    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable(){
    	WSTClearHookCache();
    	cache('hooks',null);
    	return true;
    }

    /**
     * 插件设置方法
     * @return bool
     */
    public function saveConfig(){
    	WSTClearHookCache();
    	cache('hooks',null);
    	return true;
    }
    
    /**
     * 登录页面【home】
     */
    public function homeDocumentLogin(){
    	$m = new DM();
    	$thirdLogins = $m->getThirdLogins();
    	$qqBackUrl = Url("/addon/thirdlogin-thirdlogin-qqcallback",'',true,true);
    	$weixinBackUrl = Url("/addon/thirdlogin-thirdlogin-weixincallback",'',true,true);
    	$weiboBackUrl = Url("/addon/thirdlogin-thirdlogin-weibocallback",'',true,true);
    	$thirdLogins["backUrl_qq"] = urlencode($qqBackUrl);
    	$thirdLogins["backUrl_weixin"] = urlencode($weixinBackUrl);
    	$thirdLogins["backUrl_weibo"] = urlencode($weiboBackUrl);
    	$this->assign("thirdLogins",$thirdLogins);
    	return $this->fetch('view/home/index/login');
    	
    }
    
    /**
     * 登录页面【mobile】
     */
    public function mobileDocumentLogin(){
    	$m = new DM();
    	$thirdLogins = $m->getThirdLogins();
    	$qqBackUrl = Url("/addon/thirdlogin-thirdlogin-mobileqqcallback",'',true,true);
    	$weiboBackUrl = Url("/addon/thirdlogin-thirdlogin-mobileweibocallback",'',true,true);
    	$thirdLogins["backUrl_qq"] = urlencode($qqBackUrl);
    	$thirdLogins["backUrl_weibo"] = urlencode($weiboBackUrl);
    	$this->assign("thirdLogins",$thirdLogins);
    	return $this->fetch('view/mobile/index/login');
    	 
    }
    
    /**
     * 用户注册后执行
     */
	public function afterUserRegist($params){
    	$m = new DM();
    	$thirdLogins = $m->bindAcc($params["user"]['userId']);
    }
    
    /**
     * 用户登录前执行
     */
    public function beforeUserLogin($params){
        $m = new DM();
        if($params['loginType']=='account'){
            $loginPwd = input("post.loginPwd");
            $decrypt_data = WSTRSA($loginPwd);
            if($decrypt_data['status']==1){
                $loginPwd = $decrypt_data['data'];
            }else{
                exit(json_encode(WSTReturn('登录失败')));
            }
            if($params["user"]['loginPwd']!=md5($loginPwd.$params["user"]['loginSecret'])){
                exit(json_encode(WSTReturn("密码错误!")));
            }
        }
        $rs = $m->checkBind($params["user"]['userId']);
        if($rs==false){
             exit(json_encode(WSTReturn("该帐号已绑定，请绑定其他帐号!")));
        }
    }
    

    /**
     * 用户登录后执行
     */
    public function afterUserLogin($params){
    	$m = new DM();
    	$thirdLogins = $m->bindAcc($params["user"]['userId']);
    }
    
}