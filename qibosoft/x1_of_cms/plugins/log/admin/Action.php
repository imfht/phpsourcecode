<?php
namespace plugins\log\admin;

use app\common\controller\AdminBase;

use app\common\traits\AddEditList;

use plugins\log\model\Action as ActionModel;

class Action extends AdminBase
{
	
	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items = [];
	protected $list_items;
	protected $tab_ext = [
			'page_title'=>'后台操作日志管理',
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
		$this->model = new ActionModel();
		$this->list_items = [
				 
				['uid', '用户', 'callback',function($uid){
				    return get_user_name($uid);
				}],
				['create_time', '操作时间', 'text'],
				['ip', '来源ip', 'text'],
				['model', '模块', 'text'],
		        ['controller', '控制器', 'text'],
				['action', '方法名', 'text'],				
			];
	}	

}
