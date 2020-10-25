<?php
/*
 * @varsion		EasyWork系统 1.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, 95era, Inc.
 * @link		http://www.d-winner.com
 */

class Sms_tableModel extends RelationModel{
	protected $_link = array(
		'baseinfo'=>array(
			'mapping_type'=>HAS_ONE,
			'mapping_name'=>'baseinfo',
			'class_name'=>'sms_baseinfo_table',
			'foreign_key'=>'sms_id',
		),
		'user'=>array(
			'mapping_type'=>BELONGS_TO,
			'mapping_name'=>'user',
			'class_name'=>'user_table',
			'foreign_key'=>'user_id',
			'as_fields'=>'username:username',
		),
		'receive'=>array(
			'mapping_type'=>HAS_MANY,
			'mapping_name'=>'receive',
			'class_name'=>'sms_receive_table',
			'foreign_key'=>'sms_id',
		),
	);
}