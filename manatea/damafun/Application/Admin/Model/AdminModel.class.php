<?php 
namespace Admin\Model;
use Think\Model;
class AdminModel extends Model{
		protected $_validate = array(
			array('name','require','名字不能为空'),
			array('repassword','password','两次密码输入不一致！',0,'confirm'),
		);
/* 		protected $_link = array(
			'user'=>array(
				'mapping_type'      => self::BELONGS_TO,
	            'class_name'        => 'User',
				'foreign_key'   	=> 'uid',
			),
		); */
}
?>