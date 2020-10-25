<?php
/**
 * oscshop2 B2C电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015-2016 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
namespace osc\admin\controller;
use osc\common\controller\AdminBase;
use think\Db;
class Option extends AdminBase{
	
	protected function _initialize(){
		parent::_initialize();
		$this->assign('breadcrumb1','商品');
		$this->assign('breadcrumb2','商品选项');
	}
	
    public function index(){
    	
		$list = Db::name('Option')->paginate(config('page_num'));
		
		$this->assign('list', $list);
		
		$this->assign('empty', '<tr><td colspan="20">~~暂无数据</td></tr>');
		
		return $this->fetch();  
	 }
	
	 public	function add(){
	 	
		if(request()->isPost()){
			
			$data=input('post.');	
				
			$model=osc_model('admin','option');
			
			$error=$model->validate($data);	
			if($error){
				return $error;
			}
					
			$return=$model->add_option($data);		
			
			if($return){								
				storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'新增了选项');						
				return ['success'=>'新增成功','action'=>'add'];				
			}else{			
				return ['error'=>'新增失败'];
			}
			
		}
		
		$this->assign('crumbs', '新增');
		$this->assign('action', url('Option/add'));
		return $this->fetch('edit');
		
	}
	 
	 public	function edit(){
	 	if(request()->isPost()){
	 		$data=input('post.');	
			
			$model=osc_model('admin','option'); 		
			
			$error=$model->validate($data);	
			if($error){
				return $error;
			}
					
			$return=$model->edit_option($data);		
			
			if($return){								
				storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'修改了选项');						
				return ['success'=>'修改成功','action'=>'edit'];				
			}else{			
				return ['error'=>'修改失败'];
			}
	 	}
		$this->assign('option',Db::name('Option')->find(input('id')));
		$this->assign('option_values',Db::name('OptionValue')->where('option_id',input('id'))->select());
		$this->assign('crumbs', '编辑');
		$this->assign('action', url('Option/edit'));
		return $this->fetch('edit');
	}
	 
	public function del(){
		Db::name('option')->delete(input('id'));
		Db::name('option_value')->where('option_id',input('id'))->delete();		
		storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'删除了选项');
		$this->redirect('Option/index');	
	}
	
	//获取选项
	public function autocomplete(){
		
		$filter_name=input('filter_name');
			
		$model=osc_model('admin','option');
		
		$options = $model->get_options($filter_name);
		$json=[];
		foreach ($options as $option) {
			$option_value_data = array();
			
			if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox') {
				
				$option_values = osc_goods()->get_option_values($option['option_id']);			
				
				foreach ($option_values as $option_value) {
																		
					$option_value_data[] = array(
						'option_value_id' => $option_value['option_value_id'],
						'name'            => html_entity_decode($option_value['value'], ENT_QUOTES, 'UTF-8'),								
					);
				}
				
				$sort_order = array();
			  
				foreach ($option_value_data as $key => $value) {
					$sort_order[$key] = $value['name'];
				}
		
				array_multisort($sort_order, SORT_ASC, $option_value_data);					
			}
			
			$type = '';
			
			if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' ) {
				$type = '选择';
			}
										
			$json[] = array(
				'option_id'    => $option['option_id'],
				'name'         => strip_tags(html_entity_decode($option['name'], ENT_QUOTES, 'UTF-8')),
				'category'     => $type,
				'type'         => $option['type'],
				'option_value' => $option_value_data
			);
		}
	

		$sort_order = array();
	  
		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);
		
		return 	$json;
		
	} 
}
?>