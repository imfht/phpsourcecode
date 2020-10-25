<?php
 class WxConfig extends Action{	
	
	private $cacheDir='';//缓存目录
	//得到系统配置参数
	public function get_sys_info(){
		$sql 	= "select * from fly_sys_config;";
		$list	= $this->C($this->cacheDir)->findAll($sql);
		$assArr = array();
		if(is_array($list)){
			foreach($list as $key=>$row){
				$assArr[$row["varname"]] = $row["value"];
			}
		}
		return $assArr;		
	}
	
}//end class
?>