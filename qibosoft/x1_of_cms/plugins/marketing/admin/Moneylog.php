<?php
namespace plugins\marketing\admin;

use app\common\controller\AdminBase; 

use app\common\traits\AddEditList;

use plugins\marketing\model\Moneylog as MoneylogModel;


class Moneylog extends AdminBase
{

	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items = [];
	protected $list_items;
	protected $tab_ext = [
	    'page_title'=>'用户积分使用消费记录与赚取记录',
	    'top_button'=>[ ['type'=>'delete']],
	    'right_button'=>[ ['type'=>'delete']],
	    'search'=>[
	        'uid'=>'用户UID',
	        'money'=>'积分个数',
	    ],
	];
	
	protected function _initialize()
    {
		parent::_initialize();
		$this->model = new MoneylogModel();
		//筛选字段
		$this -> tab_ext['filter_search'] = [
		        'type'=>jf_name(),
		];
		$this->list_items = [
				['uid', '用户名', 'username'],
                ['money', '数额', 'text'],
		        ['type', '分类', 'callback', function($type){
		            return jf_name($type);
		        }],
				['money', '类型', 'callback', function($value){
                    return $value>0 ? '<span style="color:red">赚取</span>' : '<span style="color:blue">消费</span>';
                }],
				['posttime', '时间', 'datetime'],
                ['about', '事项', 'text'],
                
			];
	}
	
}
