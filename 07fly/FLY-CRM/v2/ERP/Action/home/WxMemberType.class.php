<?php
class WxMemberType extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		$this->auth	 	 =_instance('Action/home/WxAuth');
	}	
	//传入ID返回名字
	public function member_type_get_name($id){
		if(empty($id)) $id=0;
		$sql  ="select id,typename from fly_member_type where id in ($id) order by id desc";	
		$list =$this->C($this->cacheDir)->findAll($sql);
		$str  ="";
		if(is_array($list)){
			foreach($list as $key=>$row){
				$str .= " ".$row["typename"]."&nbsp;";
			}
		}
		return $str;
	}
	
	

	
}//
?>