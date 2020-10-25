<?php
use Core\Model;
class meetingModel extends Model{
	protected $_validate = array(
	    array('company','require','请填写公司名称'),
	    array('name','require','请填写姓名'),
		array('mobile','require','请填写手机号'),	
	    array('mobile','mobile','请填写正确的手机号'),
	    array('mobile','','您已经报过名了，请勿重复报名！','unique'),
	    array('company','','贵公司已经报过名了，请勿重复报名！','unique'),
	);
	protected $_auto = array(
	    array('dateline','time','function'),
	);
}
