<?php
	class PayablesViewModel extends ViewModel{
		public $viewFields = array(
			'payables'=>array('payables_id','name','price','creator_role_id','owner_role_id','delete_role_id','delete_time','is_deleted','pay_time','contract_id','customer_id','create_time','update_time','description','status', '_type'=>'LEFT'),			
			'role'=>array('_on'=>'payables.creator_role_id=role.role_id', '_type'=>'LEFT'),
			'user'=>array('name'=>'creator_name', '_on'=>'role.user_id = user.user_id' ,'_type'=>'LEFT'),
			'customer'=>array('name'=>'customer_name', '_on'=>'payables.customer_id=customer.customer_id' ,'_type'=>'LEFT'),
			'contract'=>array('number'=>'contract_name', '_on'=>'payables.contract_id=contract.contract_id'),
		);
	}