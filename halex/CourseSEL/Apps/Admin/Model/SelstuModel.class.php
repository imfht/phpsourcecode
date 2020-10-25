<?php 
namespace Admin\Model;
use Think\Model;
use Think\Model\RelationModel;
class selstuModel extends RelationModel{
	
	//关联定义
	protected $_link=array(
	'stu'=>array(
	'mapping_type'=>self::BELONGS_TO,
	'class_name'=>'stu',
	//外键，也就是表Stus中的字段
	'foreign_key'=>'sid',
	'mapping_name'=>'stu',
	//关联的字段，可以多个
	'mapping_fields'=>'stuid,sex',
	'as_fields'=>'stuid:stuid,sex:sex'
		),
	);

}
 ?>