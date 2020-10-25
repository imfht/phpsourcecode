<?php
namespace app\admin\controller;

use app\common\controller\AdminBase; 

use app\common\traits\AddEditList;

use app\common\model\Jifenlog AS Model;

//积分日志
class Jifenlog extends AdminBase
{

	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items = [];
	protected $list_items;
	protected $tab_ext = [
				'help_msg'=>'用户积分使用消费记录与赚取记录',
	        'top_button'=>[ ['type'=>'delete']],
	];
	
	protected function _initialize()
    {
		parent::_initialize();
		$this->model = new Model();
		$this->list_items = [
				['uid', '用户名', 'callback', function($value){
                    return get_user($value)->username;
                }],
                ['money', '数额', 'text'],
				['money', '类型', 'callback', function($value){
                    return $value>0 ? '<span style="color:red">赚取</span>' : '<span style="color:blue">消费</span>';
                }],
				['posttime', '时间', 'datetime'],
                ['about', '事项', 'text'],
                
			];
	}
	
}
