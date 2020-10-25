<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\traits\AddEditList;
use app\common\model\Hook as HookModel;
use app\common\model\Hook_plugin as Hook_pluginModel;

class Hook extends AdminBase
{
	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items = [     
			['text', 'name', '接口标志'],
			['text', 'about', '接口描述'],
			['radio', 'ifopen', '是否启用', '', ['禁用','启用'], 1],			
	];
	protected $list_items;
	protected $tab_ext;	
	protected function _initialize()
    {
        if(!table_field('hook_plugin','hook_key')){
            into_sql(APP_PATH."common/upgrade/2.sql");
        }
		parent::_initialize();
		$this->model = new HookModel();
		$this->tab_ext = [
		        'page_title'=>'预埋接口管理',
		        'top_button'=>[
		                [
		                        'title'=>'添加接口',
		                        'url'=>url('add'),
		                        'icon'  => 'fa fa-plus-circle',
		                        'class' => '',
		                ],
		                [
		                        'title'=>'钩子管理(实现接口的功能)',
		                        'url'=>url('hook_plugin/index'),
		                        'icon'  => 'fa fa-microchip',
		                        'class' => '',
		                ],
		        ],
		];
		$this->list_items = [
				['name', '接口标志', 'text'],
		        ['about', '接口描述', 'text'],
		        ['num', '实现接口的钩子个数', 'callback',function($key,$rs){
		            $num = Hook_pluginModel::where('hook_key',$rs['name'])->count('id');
		            return $num?'<a title="查看接口(钩子)插件" icon="fa fa-microchip" class="btn btn-xs btn-default" href="'.url('hook_plugin/index',['hook_key'=>$rs['name']]).'" target="_self"><i class="fa fa-microchip"></i> '.$num.'个</a>':'无插件可用';
		        },'__data__'],
		        ['ifopen', '是否启用', 'switch'],
			];
	}
}
