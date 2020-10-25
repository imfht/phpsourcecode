<?php
use Core\Model;
class yijianModel extends Model{
	protected $_validate = array(
	    array('name','require','请填写姓名'),
		array('mobile','require','请填写手机号'),	
		array('content','require','请填写意见'),
	);
	protected $_auto = array(
	    array('dateline','time','function'),
	);
}
