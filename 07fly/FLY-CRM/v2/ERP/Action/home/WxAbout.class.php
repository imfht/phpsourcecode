<?php	 
/*
 * 店铺分类管理
 */	 
class WxAbout extends Action{	
	private $cacheDir='c_home';//缓存目录
	public function __construct() {
		_instance('Action/home/WxAuth');
	}	
	
	public function about_show(){
		$sql="select * from fly_about limit 0,1";
		$one=$this->C($this->cacheDir)->findOne($sql);
		$smarty = $this->setSmarty();
		$smarty->assign(array("one"=>$one));	
		$smarty->display('home/about_show.html');	
	}
	public function about_guest_show(){
		$sql="select * from fly_about limit 0,1";
		$one=$this->C($this->cacheDir)->findOne($sql);
		$smarty = $this->setSmarty();
		$smarty->assign(array("one"=>$one));	
		$smarty->display('home/about_guest_show.html');	
	}
}//
?>