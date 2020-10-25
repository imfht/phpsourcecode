<?php
class WxMemberAsset extends Action{	
	private $cacheDir='';//缓存目录
	private $WxMember='';//PC后台类
	public function __construct() {
		$this->auth	 	 =_instance('Action/home/WxAuth');
		$this->WxMember	 =_instance('Action/home/WxMember');
	}	
	//返回用户的基本信息
	public function member_asset_show(){
		$member=$this->WxMember->member_get_info();
		$money_address=md5($member['account']);
		
		$exchange=$this->member_exchange();
		$smarty =$this->setSmarty();
		$smarty->assign(array('member'=>$member,'money_address'=>$money_address,'exchange'=>$exchange));
		$smarty->display('home/member_asset_show.html');
	}
	
	public function member_exchange(){
		$sql	="select * from fly_exchange";
		$list 	= $this->C($this->cacheDir)->findAll($sql);
		return $list;	
	}
	
	

	
}//
?>