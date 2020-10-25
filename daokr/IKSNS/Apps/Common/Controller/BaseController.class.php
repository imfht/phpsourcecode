<?php
/*
 * IKPHP 爱客开源社区 @copyright (c) 2012-3000 IKPHP All Rights Reserved 
 * @author 小麦
 * @Email:810578553@qq.com
 * IKPHP网站基础控制器 一切控制器的入口
 */
namespace Common\Controller;
use Think\Controller;
class BaseController extends Controller {
	
    protected function _initialize() {
        //初始化网站配置
        if (false === $setting = F('setting')) {
            $setting = D('Common/Setting')->setting_cache(); 
        }
        C($setting);
        /* 爱客系统配置核心应用 修改人小麦 */
		C('DEFAULT_APPS',array('home','admin','space','develop','user','common'));
        //当前app名称
        $this->assign('app_name',strtolower(MODULE_NAME));
        //当前model名称
        $this->assign('module_name',strtolower(MODULE_NAME));
        //当前controll名称
        $this->assign('controll_name',strtolower(CONTROLLER_NAME)); 
        //当前action名称
        $this->assign('action_name',strtolower(ACTION_NAME)); 
    }
    
    public function _empty() {
    	$this->_404();
    }
    
    protected function _404($url = '') {
        if ($url) {
            redirect($url);
        } else {
            send_http_status(404);
            $this->error('呃...你想访问的页面不存在!');
            exit;
        }
    }
    //新增一个写缓存的方法
    protected function fcache($filename){
    	if (!empty($filename) && false === $setting = F($filename)) {
    		$res = M($filename)->getField('name,data');
    		foreach ($res as $key=>$val) {
    			$setting['ik_'.$key] = unserialize($val) ? unserialize($val) : $val;
    		}
    		F($filename,$setting);//写缓存
    	}
    	C(F($filename));//读缓存配置
    }
    /*
     * 更新日志；兼容tp3.2 _post _get 方法 使用 I方法
     * */    
	protected function _get($input,$filter='',$def=''){
		if(!empty($filter)){
			return I('get.'.$input,$def,$filter);
		}else{
			return I('get.'.$input,$def);
		}
	}
	protected function _post($input,$filter='',$def=''){
		if(!empty($filter)){
			return I('post.'.$input,$def,$filter);
		}else{
			return I('post.'.$input,$def);
		}
	}
}