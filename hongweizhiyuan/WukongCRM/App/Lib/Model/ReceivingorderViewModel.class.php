<?php 
	class ReceivingorderViewModel extends ViewModel{
		public $viewFields = array(
			'receivingorder'=>array('receivingorder_id','name','money','status','receivables_id','owner_role_id','delete_role_id','is_deleted','delete_time','description','pay_time','creator_role_id','create_time','update_time', '_type'=>'LEFT'),
			'receivables'=>array('name'=>'receivables_name','price'=>'price', '_on'=>'receivingorder.receivables_id=receivables.receivables_id','_type'=>'LEFT'),
			'role'=>array('_on'=>'receivingorder.creator_role_id=role.role_id', '_type'=>'LEFT'),
			'user'=>array('name'=>'creator_name', '_on'=>'role.user_id = user.user_id')
		);
	}