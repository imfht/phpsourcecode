<?php
namespace plugins\log\admin;

use app\common\controller\AdminBase;

use app\common\traits\AddEditList;

use plugins\log\model\Login as LoginModel;

class Login extends AdminBase
{
	
	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items = [];
	protected $list_items;
	protected $tab_ext = [
			'page_title'=>'后台登录日志管理',
	        'top_button'=>[ ['type'=>'delete']],
	        'right_button'=>[ ['type'=>'delete']],
// 	        'hidden_edit'=>true,	
	];
	
	protected function getOrder(){
	    return 'id desc';
	}
	
	protected function _initialize()
    {
		parent::_initialize();
		$this->model = new LoginModel();
		$this->list_items = [				 
				['username', '登录帐号', 'text'],                
				['password', '登录密码', 'callback',function($v){
				    return strlen($v)==32?'保密了':$v;
				}],
		        ['create_time', '登录时间', 'text'],
				['ip', '登录IP', 'text'],
				['password', '登录状态', 'callback',function($v){
				    return strlen($v)==32?'登录成功':'登录失败';
				}],
				
			];
	}	

}
