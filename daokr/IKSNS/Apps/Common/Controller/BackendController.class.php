<?php
/*
 * IKPHP 爱客开源社区 @copyright (c) 2012-3000 IKPHP All Rights Reserved 
 * @author 小麦 修改时间2014年3月25 22:33
 * @Email:810578553@qq.com
 * IKPHP网站后台入口基础控制器
 */
namespace Common\Controller;
use Think\Controller;

class BackendController extends Controller {

    /**
     * 后台控制器初始化
     */
    protected function _initialize(){ 
        // 获取当前用户ID
        define('UID',is_admin_login()); 
        if( !UID ){// 还没登录 跳转到登录页面
            $this->redirect('admin/public/login');
        }
        
         //初始化网站配置
        if (false === $setting = F('setting')) {
            $setting = D('Common/Setting')->setting_cache();
        }
        C($setting);
        /* 爱客系统配置核心应用 修改人小麦 */
		C('DEFAULT_APPS',array('home','admin','space','develop','user','common'));
		        
        //暂时先这样
        //网站后台seo
        $this->assign('title','IKPHP网站管理');
        $ik_soft_info = array(
        		'ikphp_version' => IKPHP_VERSION,
        		'ikphp_year' => IKPHP_YEAR,
        		'ikphp_site_name' => IKPHP_SITENAME,
        		'ikphp_site_url' => IKPHP_SITEURL,
        		'ikphp_email' => IKPHP_EMAIL,
        		 
        );

        //当前app名称
        $this->assign('module_name',strtolower(MODULE_NAME));
        //当前action名称
        $this->assign('action_name',strtolower(ACTION_NAME));
        //网站后台导航栏
        $this->app_mod = D ( 'Common/App' );
        $this->assign('admin_top_nav', $this->_getAdminNav());
        //管理员
        $admin_user = session('admin_auth');
        $this->assign('admin_user', $admin_user);
        
        $this->assign('ikphp', $ik_soft_info);
    }
    protected function title($title){
    	$this->assign('title', $title);
    }
    /**
     * 后台台分页统一
     */
    protected function _pager($count, $pagesize) {
    	$pager = new \Think\Page($count, $pagesize);
    	$pager->rollPage = 10;
    	$pager->setConfig('prev', '<前页');
    	$pager->setConfig('next', '后页>');
    	$pager->setConfig('theme', '%UP_PAGE% %FIRST% %LINK_PAGE% %END% %DOWN_PAGE%');
    	return $pager;
    }
    /**
     * 后台App导航菜单
     *
     */
    protected function _getAdminNav(){
    	$arrApp = $this->app_mod->field('app_name,app_alias,admin_entry')->order(array('display_order asc'))->select();
    	return $arrApp;
    }   
     /**
     * 修改tp3.1的方法支持 3.2的I方法
     *
     */    
	protected function _get($input,$filter='',$def=''){
		
		return I('get.'.$input,$def,$filter);
	}
	protected function _post($input,$filter='',$def=''){
		
		return I('post.'.$input,$def,$filter);
	}
    //更新配置文件
    protected function update_config($new_config, $config_file = '') {
    	if (is_writable($config_file)) {
    		$config = require $config_file;
    		$config = array_merge($config, $new_config);
    		file_put_contents($config_file, "<?php \nreturn " . stripslashes(var_export($config, true)) . ";", LOCK_EX);
    		return true;
    	} else {
    		return false;
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
}