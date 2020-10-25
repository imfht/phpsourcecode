<?php 
	class paymentorderViewModel extends ViewModel{
		public $viewFields = array(
			'paymentorder'=>array('paymentorder_id','name','money','status','payables_id','owner_role_id','delete_role_id','is_deleted','delete_time','description','pay_time','creator_role_id','create_time','update_time', '_type'=>'LEFT'),
			'payables'=>array('name'=>'payables_name','price'=>'price', '_on'=>'paymentorder.payables_id=payables.payables_id','_type'=>'LEFT'),
			'role'=>array('_on'=>'paymentorder.creator_role_id=role.role_id', '_type'=>'LEFT'),
			'user'=>array('name'=>'creator_name', '_on'=>'role.user_id = user.user_id')
		);
	}