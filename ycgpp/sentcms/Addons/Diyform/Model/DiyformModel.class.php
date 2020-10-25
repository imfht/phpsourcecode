<?php
namespace Addons\Diyform\Model;
use Think\Model;
class DiyformModel extends Model{

	protected $_auto = array(
		array('create_time','time',self::MODEL_INSERT,'function'),
		array('table','getTable',self::MODEL_INSERT,'callback'),
		array('status',1,self::MODEL_INSERT,'string'),
	);

	protected $_validate = array(
		array('falsetable','/^[A-za-z0-9_]{1,}$/','表名必须为英文或数字',0,'regex'),
	);

	//获取表名
	public function getTable(){
		return 'form_'.I('post.falsetable');
	}
}