<?php

namespace Home\Model;
use Think\Model;

class InfoSignModel extends CommonModel {
	// 自动验证设置
       
	function get_info($id){
		
		$where['info_id']=$id;		
		$where['user_id']=get_user_id();
		$data=$this->where($where)->select();
		return $data;		
	}
}	
?>