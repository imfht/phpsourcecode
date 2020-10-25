<?php 
class CommentViewModel extends ViewModel{
	public $viewFields = array(
		'comment'=>array('comment_id', 'content', 'create_time', 'update_time', 'module', 'module_id', '_type'=>'LEFT'),
		'role'=>array('user_id', 'role_id', 'position_id', '_on'=>'comment.creator_role_id=role.role_id', '_type'=>'LEFT'),
		'user'=>array('name'=>'user_name','weixinid','category_id', 'sex', 'address', 'email', 'telephone', '_on'=>'user.user_id=role.user_id',  '_type'=>'LEFT'),
		'position'=>array('name'=>'role_name', 'parent_id',  'department_id', 'description', '_on'=>'position.position_id=role.position_id', '_type'=>'LEFT'),
		'role_department'=>array('name'=>'department_name', '_on'=>'role_department.department_id=position.department_id')
	);
}