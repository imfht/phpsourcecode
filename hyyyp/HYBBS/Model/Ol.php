<?php
namespace Model;
use HY\Model;
!defined('HY_PATH') && exit('HY_PATH not defined.');
class Ol extends Model{
	public function ol($uid){
		//{hook m_ol_ol_1}
		return $this->has(array('uid'=>$uid));
	}
	public function list($size = 500){
		//{hook m_ol_list_1}
		return $this->select('*',array(
			'LIMIT' => 500,
		))
	}
	//{hook m_ol_fun}

}