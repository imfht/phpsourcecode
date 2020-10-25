<?php
class Index extends Action {
	private $cacheDir = ''; //缓存目录
	private $smarty = '';
	private $assign =array();
	
	public function __construct() {
		$this->auth=_instance('Action/home/WxAuth');
		header('Location: /index.php/home/index.php');
	}
/*	public function main(){
		$member=$this->L('home/WxMember')->member_get_info();
		$notice=$this->L('home/WxNotice')->notice();
		$banner=$this->L('home/WxNoticeBanner')->notice_banner();
		$integral=$this->member_integral_day();
		$smarty =$this->setSmarty();
		$smarty->assign(array('member'=>$member,'integral'=>$integral,'notice'=>$notice['list'],'banner'=>$banner['list']));
		$smarty->display('home/index.html');
	}
	
	//用户签到
	public function member_integral_day(){
		$member		=$this->L('home/WxMember')->member_get_info();
		$member_id	=$member['id'];
		$sql="select * from fly_integral_day where member_id='$member_id' and `adt`>=CURRENT_DATE;";
		$one= $this->C($this->cacheDir)->findOne($sql);
		if(empty($one)){
			$rtn=$this->member_integral_day_add($member_id);
		}else{
			$rtn='0';
		}
		return $rtn;
	}
	
	//每天执行之后就生成一条记录
	public function member_integral_day_add($member_id){
		$dt	   		= date("Y-m-d H:i:s",time());
		
		$sql="select * from fly_member where id='$member_id'";
		$one= $this->C($this->cacheDir)->findOne($sql);
		$member_integral=$one['integral'];
		
		//查询交换比例
		$day_sql="select * from fly_conf_day limit 0,1";
		$day_cfg=$this->C($this->cacheDir)->findOne($day_sql);

		$day_integral=$member_integral*$day_cfg['rate'];
		//开启事务
		$this->C($this->cacheDir)->begintrans();
		//插入兑换记录
		$sql="insert into fly_integral_day(member_id,balance,integral,adt) 
								values('$member_id','$day_integral','-$day_integral','$dt')";
		$rtn=$this->C($this->cacheDir)->update($sql);
		if($rtn>0){
			$this->C($this->cacheDir)->rollback();
		}
		//更新会员余额和积分
		$u_sql="update fly_member set balance=balance+$day_integral,integral=integral-$day_integral where id='$member_id'";
		$u_rtn=$this->C($this->cacheDir)->update($u_sql);
		if($u_rtn>0){
			$this->C($this->cacheDir)->rollback();
		}
		//事件提交
		$this->C($this->cacheDir)->commit();
		
		return round($day_integral,2);
	}*/
	
} //
?>