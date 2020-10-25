<?php 
	class ContactsViewModel extends ViewModel {
	   public $viewFields = array(
		'contacts'=>array('contacts_id','creator_role_id','name','post','department','sex','saltname','telephone','email','qq','address','zip_code','description','create_time','update_time','is_deleted','delete_role_id','delete_time','_type'=>'LEFT'),
		'RContactsCustomer'=>array('_on'=>'contacts.contacts_id=RContactsCustomer.contacts_id','_type'=>'LEFT'),
		'customer'=>array('customer_id','owner_role_id','name'=>'customer_name','_on'=>'customer.customer_id=RContactsCustomer.customer_id')
	   );
	} 