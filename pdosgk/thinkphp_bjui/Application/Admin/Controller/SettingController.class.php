<?php
/**
 * 
 * 相关设置
 * @author Lain
 *
 */
namespace Admin\Controller;
use Admin\Controller\AdminController;
class SettingController extends AdminController{
	public function _initialize(){
		$action = array(
				'permission'=>array('profile', 'changePassword', 'ajax_checkUsername'),
				//'allow'=>array('index')
		);
		B('Admin\\Behaviors\\Authenticate', '', $action);
	}
	
	public function site(){

		if(IS_POST){
			$info = I('post.info');
			$setting = I('post.setting');
			$info['setting'] = array2string($setting);
			D('Site')->update($info);
			$this->ajaxReturn(array('statusCode'=>200,'tabid'=>'Setting_site'));
		}else{
			$detail = D('Site')->getSetting();
			$template_list = template_list();

			$this->assign('Detail', $detail);
			$this->assign('setting', $detail['setting']);
			$this->assign('template_list', $template_list);
			$this->display();
		}
		
	}
}