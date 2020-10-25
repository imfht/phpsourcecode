<?php
class WxMemberNotice extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		$this->auth=_instance('Action/home/WxAuth');
	}	
	public function member_notice(){
		$member	  	 =$this->L('home/WxMember')->member_get_info();
		$where_str 	 ="member_id='".$member['id']."' ";
		$sql		 = "select * from fly_member_notice where $where_str order by id desc";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list);	
		return $assignArray;
		
	}
	public function member_notice_show(){
		$assArr  = $this->member_notice();
		$smarty  = $this->setSmarty();
		$smarty->assign($assArr);
		$smarty->display('home/member_notice_show.html');	
	}

	
	
	public function member_notice_show_one(){
		$id	  	 = $this->_REQUEST("id");
		$sql 		= "select * from fly_member_notice where id='$id'";
		$one 		= $this->C($this->cacheDir)->findOne($sql);	
		$smarty 	= $this->setSmarty();
		$smarty->assign(array("one"=>$one));
		$smarty->display('home/member_notice_show_one.html');	

	}	

}//
?>