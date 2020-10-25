<?php 
	class MessageReceiveViewModel extends ViewModel{
		public $viewFields = array(
			'message'=>array('message_id','to_role_id','from_role_id','content','read_time','send_time','_type'=>'LEFT'),
			'role'=>array('user_id', 'role_id', 'position_id', '_on'=>'role.role_id = message.from_role_id', '_type'=>'LEFT'),
			'user'=>array('name'=>'from_name','weixinid','category_id', 'sex', 'address', 'email', 'telephone', '_on'=>'user.user_id=role.user_id')
		);

	}