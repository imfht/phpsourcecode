<?php
/**
 * oscshop 电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
namespace Admin\Controller;
use Admin\Model\AdminUserModel;

class AdminUserController extends CommonController{
	
	protected function _initialize(){
		parent::_initialize();
			$this->breadcrumb1='用户';
			$this->breadcrumb2='后台用户';
	}
	
     public function index(){
     	
		$model=new AdminUserModel();   
						
		$data=$model->show_admin_user_page();	
		
		$this->assign('empty',$data['empty']);// 赋值数据集
		$this->assign('list',$data['list']);// 赋值数据集
		$this->assign('page',$data['page']);// 赋值分页输出	
		
    	$this->display();
	 }
	
	function add(){
		$model=new AdminUserModel();
		if(IS_POST){			  
			$data=I('post.');
			$return=$model->add_admin_user($data);			
			$this->osc_alert($return);
		}
		$this->crumbs='新增';		
		$this->action=U('AdminUser/add');
		
		$this->display();		
	}
	
	function info(){
		$model=new AdminUserModel();
		if(IS_POST){			  
			$data=I('post.');
			$return=$model->edit_admin_user($data);			
			$this->osc_alert($return);
		}
		$this->crumbs='编辑';		
		$this->action=U('AdminUser/info');
		$this->data=M('Admin')->find(I('id'));
	
		$this->display();		
	}
	 
}
?>