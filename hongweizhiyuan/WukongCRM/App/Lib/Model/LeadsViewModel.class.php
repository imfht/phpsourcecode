<?php 
	class LeadsViewModel extends ViewModel{
		public $viewFields;
		public function _initialize(){
			$main_must_field = array('leads_id','creator_role_id','owner_role_id','create_time','update_time','contacts_id','customer_id','is_deleted');
            
			$main_list = array_unique(array_merge(M('Fields')->where(array('model'=>'leads','is_main'=>1))->getField('field', true),$main_must_field));
			$main_list['_type'] = 'LEFT';
			$data_list = M('Fields')->where(array('model'=>'leads','is_main'=>0))->getField('field', true);
			$data_list['_on'] = 'leads.leads_id = leads_data.leads_id';
            $data_list['_type'] = 'LEFT';
			
			$this->viewFields = array(
				'leads'=>$main_list,
				'leads_data'=>$data_list, 
			);
		}

	}