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
class Goods extends AdminBase{
	
	protected function _initialize(){
		parent::_initialize();
		$this->assign('breadcrumb1','商品');
		$this->assign('breadcrumb2','商品管理');
	}
	//商品列表
    public function index(){

		$filter=input('param.');
        
		if(isset($filter['type'])&&$filter['type']=='search'){
			$list=osc_goods()->get_category_goods_list($filter,config('page_num'));
		}else{
			$list=osc_goods()->get_goods_list($filter,config('page_num'));
		}		

		$this->assign('empty','<tr><td colspan="20">没有数据~</td></tr>');
		
		$this->assign('category',osc_goods()->get_category_tree());
		
		$this->assign('list',$list);
	
		return $this->fetch();

	 }
	 //新增商品
	 public function add(){
		
		if(request()->isPost()){
			
			$data=input('post.');
			
			$model=osc_model('admin','goods');  	
			
			$error=$model->validate($data);	
	
			if($error){					
				$this->error($error['error']);	
			}
			
			$return=$model->add_goods($data);		
			
			if($return){
												
				storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'新增了商品');		
			
				$this->success('新增成功！',url('Goods/index'));			
			}else{
				$this->error('新增失败！');			
			
			}
			
		}
		
		$this->assign('weight_class',Db::name('WeightClass')->select());
		$this->assign('length_class',Db::name('LengthClass')->select());
	 	$this->assign('crumbs', '新增');
		$this->assign('action', url('Goods/add'));
		
	 	return $this->fetch('edit');
	 }
	 //商品基本信息
	 public function edit_general(){
	 	
		if(request()->isPost()){
			
			$data=input('post.');
			
			if(empty($data['name'])){
		
				$this->error('商品名称必填！');	
			}
			
			$description=$data['description'];
			unset($data['description']);
			
			
			try{
				
				Db::name('goods')->update($data,false,true);
				Db::name('goods_description')->where('goods_id',$data['goods_id'])->update($description,false,true);
				storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'更新商品基本信息');							
				return $this->success('更新成功！',url('Goods/index'));
				
			}catch(Exception $e){
				return $this->error('更新失败！'.$e);	
			}
			
		}
		
		$this->assign('weight_class',Db::name('WeightClass')->select());
		$this->assign('length_class',Db::name('LengthClass')->select());
		$this->assign('description',Db::name('goods_description')->where('goods_id',(int)input('id'))->find());
	 	$this->assign('goods',Db::name('Goods')->find((int)input('id')));
		
	 	$this->assign('crumbs', '编辑基本信息');	
		
	 	return $this->fetch('general');
	 }
	 //商品关联项	
	 public function edit_links(){
	 	
		if(request()->isPost()){				
				
				$resault=osc_model('admin','goods')->edit_links(input('post.'));					
					
				if($resault){
					storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'更新商品分类');											
					return $this->success('更新成功！',url('Goods/index'));
				}else{
					return $this->error('更新失败！');
				}				
		
		}
		
		$link_data=osc_model('admin','goods')->get_link_data((int)input('param.id'));  
		
	 	$this->assign('goods_categories',$link_data['goods_categories']);
		
		$this->assign('goods_attribute',$link_data['goods_attribute']);
		
		$this->assign('goods_brand',$link_data['goods_brand']);
		
	 	$this->assign('crumbs', '关联');	
		
	 	return $this->fetch('links');
	 }
	 //商品选项
	 public function edit_option(){
	 	
		if(request()->isPost()){
				
			$data=input('post.');
			
			if (isset($data['goods_option'])) {
				foreach ($data['goods_option'] as $goods_option) {
					
					if(!isset($goods_option['goods_option_value'])){					
						$this->error('选项值必填');
					}
								
					foreach ($goods_option['goods_option_value'] as $k => $v) {
						if((int)$v['quantity']<=0){
							$this->error('数量必填');
						}
					}
				}
			}
			$model=osc_model('admin','goods'); 
			
			$model->edit_option($data);
			
			storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'更新商品选项');	
									
			return $this->success('更新成功！',url('Goods/index'));

		}		
		
		$goods_options=osc_goods()->get_goods_options(input('id'));
		
		$this->assign('goods_options',$goods_options);	

		//选项值
		$option_values=[];
		foreach ($goods_options as $goods_option) {
				$option_values[$goods_option['option_id']] = osc_goods()->get_option_values($goods_option['option_id']);
		}		
		
		$this->assign('option_values',$option_values);	
		
		$this->assign('crumbs', '选项');	
	 	return $this->fetch('option');
	 }
	 //商品折扣
	 public function edit_discount(){		
		
		$this->assign('goods_discount',Db::name('goods_discount')->where('goods_id',input('id'))->order('quantity ASC')->select());	
		$this->assign('crumbs', '折扣');	
	 	return $this->fetch('discount');
	 }
	 //商品相册
	 public function edit_image(){
	 	$this->assign('goods_images',Db::name('goods_image')->where('goods_id',input('id'))->order('sort_order asc')->select());	
		$this->assign('crumbs', '商品相册');	
	 	return $this->fetch('image');
	 }
	 //商品手机端描述
	 public function edit_mobile(){
	 	$this->assign('mobile_images',Db::name('goods_mobile_description_image')->where('goods_id',input('id'))->order('sort_order asc')->select());	
		$this->assign('crumbs', '手机端描述');	
	 	return $this->fetch('mobile');
	 }
	 
	//编辑信息，新增，修改
	function ajax_eidt(){
		if(request()->isPost()){
			
			$data=input('post.');
			
			$table_name=$data['table'];
			
			if(isset($data[$table_name][$data['key']])){
				$info=$data[$table_name][$data['key']];
			}	
			
			if($table_name=='goods_discount'){
				if(!is_numeric($info['quantity'])||!is_numeric($info['price']))
				return ['error'=>'请输入数字'];
			}
			
			if(isset($data['id'])&&$data['id']!=''){
				//更新
				$info[$data['pk_id']]=(int)$data['id'];				
								
				$r=Db::name($table_name)->update($info,false,true);
				if($r){
					storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'更新商品'.$data['id']);	
					return ['success'=>'更新成功'];
				}else{
					return ['error'=>'更新失败'];
				}
			}else{
				//新增
				$info['goods_id']=(int)$data['goods_id'];
		
				$r=Db::name($table_name)->insert($info,false,true);
				if($r){
					storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'更新商品'.$data['goods_id']);						
					return ['success'=>'更新成功','id'=>$r];
				}else{
					return ['error'=>'更新失败'];
				}
			}
		}
	}
	//用于编辑中删除
	 function ajax_del(){
		if(request()->isPost()){
			$data=input('post.');		
			
			if(empty($data['id'])){
				return ['success'=>'删除成功'];
			}
			
			$r=Db::name($data['table'])->delete($data['id']);
			
			if($r){
				return ['success'=>'删除成功'];
			}else{
				return ['error'=>'删除失败'];
			}
		}
	}
	//复制商品 
	function copy_goods(){
		$id =input('post.');

		$model=osc_model('admin','goods'); 
		 	
		if($id){		
			foreach ($id['id'] as $k => $v) {						
				$model->copy_goods((int)$v);
			}	
			storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'复制商品');
			
			$data['redirect']=url('Goods/index');	
						
			return $data;
		}
	}
	//删除商品
	function del(){
		
		$model=osc_model('admin','goods'); 
			
		$r=$model->del_goods((int)input('param.id'));	
		
		if($r){
			
			storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'删除商品'.input('get.id'));
			
			$this->redirect('Goods/index');
			
		}else{
			
			return $this->error('删除失败！',url('Goods/index'));
		}		
		
	}
	//更新状态
	function set_status(){
		$data=input('param.');
		
		$update['goods_id']=(int)$data['id'];
		$update['status']=(int)$data['status'];
		
		if(Db::name('goods')->update($update)){
			storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'更新商品状态');
			$this->redirect('Goods/index');
		}
	}	
	//更新价格
	function update_price(){
		$data=input('post.');
		
		$update['goods_id']=(int)$data['goods_id'];
		$update['price']=(float)$data['price'];
		
		if(Db::name('goods')->update($update)){
			storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'更新商品价格');
			return true;
		}		
	}
	//更新数量
	function update_quantity(){
		$data=input('post.');
		
		$update['goods_id']=(int)$data['goods_id'];
		$update['quantity']=(int)$data['quantity'];
		
		if(Db::name('goods')->update($update)){
			storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'更新商品数量');
			return true;
		}		
	}
	//更新排序
	function update_sort(){
		$data=input('post.');
		
		$update['goods_id']=(int)$data['goods_id'];
		$update['sort_order']=(int)$data['sort'];
		
		if(Db::name('goods')->update($update)){
			storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'更新商品排序');
			return true;
		}		
	}
	
}
?>