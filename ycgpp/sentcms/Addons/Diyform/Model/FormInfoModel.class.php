<?php 
namespace Addons\Diyform\Model;
use Think\Model;
class FormInfoModel extends Model{
	protected $_auto = array(
		array('textname','getTextname',self::MODEL_INSERT,'callback'),
	);

	protected $_validate = array(
		array('fieldname','/^[A-Za-z0-9_]{1,}$/','字段名称必须是英文或数字',0,'regex'),
		array('fieldlength','1,3','字段长度必须为数字1-3位之间',2,'length'),
	);

	public function getTextname(){
		return I('post.fieldname');
	}
}