<?php
class WxNoticeBanner extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		$this->auth=_instance('Action/home/WxAuth');
	}	
	public function notice_banner(){
		$sql		 = "select * from fly_notice_banner order by id desc";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list);	
		return $assignArray;
		
	}
	public function notice_banner_show(){
		$assArr  = $this->notice_banner();
		$smarty  = $this->setSmarty();
		$smarty->assign($assArr);
		$smarty->display('home/notice_banner_show.html');	
	}

	
	
	public function notice_banner_show_one(){
		$id	  	 = $this->_REQUEST("id");
		$sql 		= "select * from fly_notice_banner where id='$id'";
		$one 		= $this->C($this->cacheDir)->findOne($sql);	
		$smarty 	= $this->setSmarty();
		$smarty->assign(array("one"=>$one));
		$smarty->display('home/notice_banner_show_one.html');	

	}	

}//
?>