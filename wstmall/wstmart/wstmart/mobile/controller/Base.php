<?php
namespace wstmart\mobile\controller;
use think\Controller;
use think\Db;

/**
 * ============================================================================
 * WSTMart多用户商城
 * 版权所有 2016-2066 广州商淘信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.wstmart.net
 * 交流社区:http://bbs.shangtao.net
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 基础控制器
 */
class Base extends Controller {
	public function __construct(){
		parent::__construct();
		hook('initConfigHook',['getParams'=>input()]);
		WSTConf('CONF',WSTConfig());
		//WSTSwitchs();
		$this->assign("v",WSTConf('CONF.wstVersion')."_".WSTConf('CONF.wsthomeStyleId'));
		$this->view->filter(function($content){
            $style = WSTConf('CONF.wstmobileStyle')?WSTConf('CONF.wstmobileStyle'):'default';
            $content = str_replace("__RESOURCE_PATH__",WSTConf('CONF.resourcePath'),$content);
            $content = str_replace("__MOBILE__",str_replace('/index.php','',$this->request->root()).'/wstmart/mobile/view/'.$style,$content);
            return $content;
        });
		if(WSTConf('CONF.seoMallSwitch')==0){
			$this->redirect('mobile/switchs/index');
			exit;
		}
	}
    // 权限验证方法
    protected function checkAuth(){
       	$USER = session('WST_USER');
        if(empty($USER)){
        	if(request()->isAjax()){
        		die('{"status":-999,"msg":"您还未登录"}');
        	}else{
        		$this->redirect('mobile/users/login');
        		exit;
        	}
        }
    }

    // 店铺权限验证方法
    protected function checkShopAuth($opt){
       	$shopMenus = WSTShopOrderMenus();
       	if($opt=="list"){
       		if(count($shopMenus)==0){
       			session('moshoporder','对不起,您无权进行该操作');
       			$this->redirect('mobile/error/message',['code'=>'moshoporder']);
		    	exit;
       		}
       	}else{
       		if(!array_key_exists($opt,$shopMenus)){
	       		if(request()->isAjax()){
		    		die('{"status":-1,"msg":"您无权进行该操作"}');
		    	}else{
		    		session('moshoporder','对不起,您无权进行该操作');
		    		$this->redirect('mobile/error/message',['code'=>'moshoporder']);
		    		exit;
		    	}
	       	}
       	}
    }
	protected function fetch($template = '', $vars = [], $config = []){
		$style = WSTConf('CONF.wstmobileStyle')?WSTConf('CONF.wstmobileStyle'):'default';
		return $this->view->fetch($style."/".$template, $vars, $config);
		
	}
	/**
	 * 上传图片
	 */
	public function uploadPic(){
		$this->checkAuth();
		return WSTUploadPic(0);
	}
	/**
	 * 获取验证码
	 */
	public function getVerify(){
		WSTVerify();
	}
}