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
class Attribute extends AdminBase{
	
	protected function _initialize(){
		parent::_initialize();
		$this->assign('breadcrumb1','商品');
		$this->assign('breadcrumb2','商品属性');
	}
	
    public function index(){			
		
		$list = Db::name('attribute')->paginate(config('page_num'));
		
		$this->assign('empty','<tr><td colspan="20">没有数据~</td></tr>');
		
		$this->assign('list', $list);		
		
		$this->assign('category',osc_goods()->get_category_tree()); 
		
		return $this->fetch();  
	 }
	
	 public	function add(){
	 	
		if(request()->isPost()){
			
			$data=input('post.');	
			
			$model=osc_model('admin','attribute'); 
			
			$error=$model->validate($data);	
			if($error){
				return $error;
			}
					
			$return=$model->add_attribute($data);		
			
			if($return){								
				storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'新增了属性');						
				return ['success'=>'新增成功','action'=>'add'];				
			}else{			
				return ['error'=>'新增失败'];
			}
			
		}
		
		$this->assign('crumbs', '新增');
		$this->assign('category',osc_goods()->get_category_tree());
		$this->assign('action', url('Attribute/add'));
		return $this->fetch('edit');
		
	}
	 
	 public	function edit(){
	 	if(request()->isPost()){
	 		
	 		$data=input('post.');	
			
			$model=osc_model('admin','attribute');  		
			
			$error=$model->validate($data);	
			if($error){
				return $error;
			}
					
			$return=$model->edit_attribute($data);		
			
			if($return){								
				storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'修改了属性');						
				return ['success'=>'修改成功','action'=>'edit'];				
			}else{			
				return ['error'=>'修改失败'];
			}
	 	}
		
		$this->assign('category',osc_goods()->get_category_tree());
		$this->assign('attribute',Db::name('attribute')->find((int)input('id')));
		$this->assign('attribute_values',Db::name('attribute_value')->where('attribute_id',(int)input('id'))->select());
		$this->assign('crumbs', '编辑');
		$this->assign('action', url('Attribute/edit'));
		return $this->fetch('edit');
	}
	 
	public function del(){
		Db::name('attribute')->delete((int)input('id'));
		Db::name('attribute_value')->where('attribute_id',(int)input('id'))->delete();	
		storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'删除了属性');		
		$this->redirect('Attribute/index');	
	}
	//用于自动完成
	public function autocomplete(){
				
		$filter_name=input('param.filter_name');
		
		if (isset($filter_name)) {			
			$sql='SELECT attribute_value_id,a.name,value_name FROM '.config('database.prefix')."attribute a left join "
			.config('database.prefix')."attribute_value av on a.attribute_id=av.attribute_id where a.value LIKE'%".$filter_name."%' LIMIT 0,20";				
		}else{
			$sql='SELECT attribute_value_id,a.name,value_name FROM '.config('database.prefix')."attribute a left join "
			.config('database.prefix')."attribute_value av on a.attribute_id=av.attribute_id LIMIT 0,20";		
		}		
		
		$results = Db::query($sql);
		$json=[];
		foreach ($results as $result) {
				$json[] = array(
					'attribute_id'    => $result['attribute_value_id'],
					'name'            => strip_tags(html_entity_decode($result['value_name'], ENT_QUOTES, 'UTF-8')),
					'attribute_group' => $result['name']
				);
			}
		

		return 	$json;
	}
	//用于自动完成
	public function get_attribute_group(){
				
		$filter_name=input('filter_name');
		
		if (isset($filter_name)) {			
			$sql='SELECT attribute_id,name,value FROM '.config('database.prefix')."attribute where value LIKE'%".$filter_name."%' LIMIT 0,20";				
		}else{
			$sql='SELECT attribute_id,name,value FROM '.config('database.prefix')."attribute LIMIT 0,20";				
		}		
		
		$results = Db::query($sql);
		$json=[];
		foreach ($results as $result) {
				$json[] = array(
					'attribute_id'    => $result['attribute_id'],
					'value'            => strip_tags(html_entity_decode($result['value'], ENT_QUOTES, 'UTF-8')),
					'attribute_group' => $result['name']
				);
			}
		

		return 	$json;
	}
	
}
?>