<?php
namespace app\admin\controller;
die('此文件作废.可以删除!');
use app\common\controller\AdminBase; 

use app\common\traits\AddEditList;

use app\common\model\Group as GroupModel;

class WebGroup extends AdminBase
{
	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items = [];
	protected $list_items;
	protected $tab_ext = [
				'id'=>false,
				'help_msg'=>'前台用户组管理',
				];
	
	protected function _initialize()
    {
		parent::_initialize();
		$this->model = new GroupModel();
		$this->list_items = [
				['gid', '用户组ID', 'text'],
				['grouptitle', '用户组名称', 'text.edit'],
				['gptype', '用户组性质', 'callback', function($value){
                    return $value==1 ? '高级系统组' : '普通会员组';
                }],                
				['allowadmin', '后台权限', 'switch'],
				['levelnum', '升级积分', 'text.edit'],
			];
	}
	
	protected function getMap()
    {
		return array_merge( parent::getMap() , ['allowadmin'=>0] );		
	}
	
	protected function getOrder($extra_order = '', $before = false)
    {
		if( empty( parent::getOrder($extra_order, $before) ) ){
			
			return 'levelnum ASC';
		}
	}

	public function edit($id){
		header('location:'.url('group/edit',"id=$id"));
		exit;
	}

	public function add(){
		header('location:'.url('group/add'));
		exit;
	}

}
