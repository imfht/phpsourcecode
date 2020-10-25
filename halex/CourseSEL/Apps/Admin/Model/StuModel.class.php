<?php 
namespace Admin\Model;
use Think\Model;
use Think\Model\RelationModel;
class StuModel extends RelationModel{
	
	//关联定义
	protected $_link=array(
	'stuin'=>array(
	'mapping_type'=>self::BELONGS_TO,
	'class_name'=>'stuin',
	//外键，也就是表Stus中的字段
	'foreign_key'=>'inid',
	'mapping_name'=>'stuin',
	//关联的字段，可以多个
	'mapping_fields'=>'title',
	'as_fields'=>'title:stuin_title'
		),
	);

}
 ?>