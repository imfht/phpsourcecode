<?php
class WxMemberLog extends Action{	
	private $cacheDir='';//缓存目录
	private $member	='';//
	private $member_id	='';//
	public function __construct() {
		$this->auth=_instance('Action/home/WxAuth');
		$this->member	=$this->L('home/WxMember')->member_get_info();
		$this->member_id=$this->member['id'];
	}	
	
	public function member_log_balance(){
		
	}
	
	//余额记录
	public function member_log_balance_show(){
		//加速记录
		$quick_sql	="select balance,pay_adt from fly_member_quick where member_id='$this->member_id' and ifpay='1'";
		$quick_list	= $this->C($this->cacheDir)->findAll($quick_sql);
		//签到
		$days_sql="select balance,pay_adt from fly_integral_day where member_id='$this->member_id' and ifpay='1'";
		$days_list	= $this->C($this->cacheDir)->findAll($days_sql);
		//print_r($days_list);
		//转出
		
		$smarty  = $this->setSmarty();
		$assignArray = array('quick_list'=>$quick_list,'days_list'=>$days_list);	
		$smarty->assign($assignArray);
		$smarty->display('home/member_log_balance_show.html');	
		
	}
	
	public function member_log_integral_show(){
		//加速记录
		$quick_sql	="select integral,pay_adt from fly_member_quick where member_id='$this->member_id' and ifpay='1'";
		$quick_list	= $this->C($this->cacheDir)->findAll($quick_sql);
		//签到
		$days_sql="select integral,pay_adt from fly_integral_day where member_id='$this->member_id' and ifpay='1'";
		$days_list	= $this->C($this->cacheDir)->findAll($days_sql);
		//print_r($days_list);

		//vip
		$vip_sql="select integral,pay_adt from fly_integral_vip where member_id='$this->member_id' and ifpay='1'";
		$vip_list	= $this->C($this->cacheDir)->findAll($vip_sql);
		//print_r($days_list);
		
		$smarty  = $this->setSmarty();
		$assignArray = array('quick_list'=>$quick_list,'days_list'=>$days_list,'vip_list'=>$vip_list);	
		$smarty->assign($assignArray);
		$smarty->display('home/member_log_integral_show.html');	
		
	}
	
	
}//
?>