<?php 
	class contractViewModel extends ViewModel {
	   public $viewFields = array(
		'contract'=>array('contract_id','number','business_id','is_deleted','start_date','end_date','delete_role_id','delete_time','price','due_time','content','creator_role_id','owner_role_id','description','create_time','update_time','status','_type'=>'LEFT'),
		'business'=>array('name'=>'business_name','contacts_id'=>'contacts_id','customer_id'=>'customer_id', '_on'=>'contract.business_id=business.business_id','_type'=>'LEFT'),
		'contacts'=>array('name'=>'contacts_name', '_on'=>'contacts.contacts_id=business.contacts_id','_type'=>'LEFT'),
		'customer'=>array('name'=>'customer_name', '_on'=>'customer.customer_id=business.customer_id','_type'=>'LEFT'),
		'user'=>array('name'=>'owner_name', '_on'=>'contract.owner_role_id=user.role_id')
	   );
	} 