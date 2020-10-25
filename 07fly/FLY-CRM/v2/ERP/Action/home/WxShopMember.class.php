<?php
/*
 * 店铺管理类
 */	
class WxShopMember extends Action{	
	private $cacheDir='';//缓存目录
	private $type='';//缓存目录
	private $member='';//缓存目录
	public function __construct() {
		$this->auth=_instance('Action/home/WxAuth');
		$this->type=_instance('Action/admin/ShopType');
		$this->member=_instance('Action/admin/Member');
		
	}	
	//会员店铺
	public function shop_member_show(){
		$member		 =$this->L('home/WxMember')->member_get_info();
		$member_id  =$member['id'];
		$sql		 = "select * from fly_shop where member_id='$member_id' order by id desc;";	
		$one		 = $this->C($this->cacheDir)->findOne($sql);
		$shop_type	 =$this->L('home/WxShopType')->shop_type();
		$smarty = $this->setSmarty();
		if(empty($one)){
			$smarty->assign(array('shop_type'=>$shop_type));
			$smarty->display('home/shop_add.html');
		}else{
			if($one['status']==0){//表示未审核
				$smarty->display('home/shop_add_audit.html');	
			}elseif($one['status']==2){//表示拒绝
				$smarty->assign(array('one'=>$one));
				$smarty->display('home/shop_add_reject.html');	
			}else{//表示已经存在店铺
				$shop_id=$one['id'];
				$sql	="select * from fly_product where shop_id='$shop_id' order by id desc";
				$list	= $this->C($this->cacheDir)->findAll($sql);
				$smarty->assign(array('list'=>$list,'shop'=>$one));
				$smarty->display('home/shop_member_show.html');					
			}
		}
	}


	
}//
?>