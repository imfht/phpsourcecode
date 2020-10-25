<?php 
	class ProductViewModel extends ViewModel{
		public $viewFields;
		public function _initialize(){
			$main_must_field = array('product_id','creator_role_id','create_time','update_time');
            
			$main_list = array_unique(array_merge(M('Fields')->where(array('model'=>'product','is_main'=>1))->getField('field', true),$main_must_field));
			$main_list['_type'] = 'LEFT';
			$data_list = M('Fields')->where(array('model'=>'product','is_main'=>0))->getField('field', true);
			$data_list['_on'] = 'product.product_id = product_data.product_id';
            $data_list['_type'] = 'LEFT';
			
			$this->viewFields = array(
				'product'=>$main_list,
				'product_data'=>$data_list, 
				'product_category'=>array('name'=>'category_name', '_on'=>'product.category_id=product_category.category_id', '_type'=>'LEFT'),
			);
		}
	}