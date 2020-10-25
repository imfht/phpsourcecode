<?php 
class RoleViewModel extends ViewModel{
	public $viewFields = array(
		'role'=>array('user_id', 'role_id', 'position_id', '_type'=>'LEFT'),
		'user'=>array('name'=>'user_name','status','weixinid','category_id', 'sex', 'address', 'email','img', 'telephone','dashboard', '_on'=>'user.user_id=role.user_id',  '_type'=>'LEFT'),
		'position'=>array('name'=>'role_name', 'parent_id',  'department_id', 'description','_on'=>'position.position_id=role.position_id', '_type'=>'LEFT'),
		'role_department'=>array('name'=>'department_name', '_on'=>'role_department.department_id=position.department_id')
	);
}