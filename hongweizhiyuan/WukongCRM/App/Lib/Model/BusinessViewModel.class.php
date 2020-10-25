<?php
	class BusinessViewModel extends ViewModel {
        public $viewFields;
		public function _initialize(){
			$main_must_field = array('business_id','owner_role_id','creator_role_id','delete_role_id','create_time','is_deleted','delete_time','update_time','update_role_id');
            //$additional = array('total_amount','subtotal_val','discount_price','sales_price');
			$main_list = array_unique(array_merge(M('Fields')->where(array('model'=>'business','is_main'=>1))->getField('field', true),$main_must_field));
			$main_list['_type'] = 'LEFT';
			$data_list = M('Fields')->where(array('model'=>'business','is_main'=>0))->getField('field', true);
			$data_list['_on'] = 'business.business_id = business_data.business_id';
            $data_list['_type'] = 'LEFT';
			
			$this->viewFields = array(
				'business'=>$main_list,
				'business_data'=>$data_list, 
				'business_status'=>array('name'=>'status_name', '_on'=>'business.status_id=business_status.status_id', '_type'=>'LEFT'),
				'contacts'=>array('name'=>'contacts_name', '_on'=>'business.contacts_id = contacts.contacts_id')
			);
		}
	}