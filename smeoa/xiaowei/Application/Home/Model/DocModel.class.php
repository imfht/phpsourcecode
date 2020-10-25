<?php
/*---------------------------------------------------------------------------
  小微OA系统 - 让工作更轻松快乐 

  Copyright (c) 2013 http://www.smeoa.com All rights reserved.                                             


  Author:  jinzhu.yin<smeoa@qq.com>                         

  Support: https://git.oschina.net/smeoa/xiaowei               
 -------------------------------------------------------------------------*/

namespace Home\Model;
use Think\Model;

class  DocModel extends CommonModel {
	// 自动验证设置
	protected $_validate	 =	 array(
		array('name','require','文件名必须',1),
		array('content','require','内容必须'),
		);

	function _before_insert(&$data,$options){
        $sql = "SELECT CONCAT(year(now()),'-',LPAD(count(*)+1,4,0)) doc_no FROM `".$this->tablePrefix."doc` WHERE 1 and year(FROM_UNIXTIME(create_time))>=year(now())";       
        $rs = $this->db->query($sql);
        if($rs){
            $data['doc_no']=$rs[0]['doc_no'];    
        }else{
            $data['doc_no']=date('Y')."-0001"; 
        }
	}
}	
?>