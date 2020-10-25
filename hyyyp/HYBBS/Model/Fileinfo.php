<?php
namespace Model;
use HY\Model;
!defined('HY_PATH') && exit('HY_PATH not defined.');
class Fileinfo extends Model {

	//获取文件信息
	public function read($id){
		//{hook m_fileinfo_read_1}
		return $this->find("*",array('id'=>$id));
	}
	//{hook m_fileinfo_fun}
	
}