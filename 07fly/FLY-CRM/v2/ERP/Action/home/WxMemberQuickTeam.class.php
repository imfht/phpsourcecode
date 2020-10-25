<?php
/*
 * 产品管理类
 */	
class WxMemberQuickTeam extends Action{	
	private $cacheDir='';//缓存目录
	private $type='';//缓存目录
	private $member='';//缓存目录
	private $shop='';//缓存目录
	public function __construct() {
		$this->auth=_instance('Action/home/WxAuth');		
		$this->member=_instance('Action/home/WxMember');		
	}

	//团队佣金
	public function member_quick_team(){
		$member=$this->member->member_get_info();
		$p_id  =$member['member_id'];
		$member_tree=$this->L('admin/MemberTree');
		
		//**获得传送来的数据作分页处理
		$currentPage= $this->_REQUEST("pageNum");//第几页
		$numPerPage	= 10;//每页多少条
		$currentPage= empty($currentPage)?1:$currentPage;
		$numPerPage = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		$beginRecord= ($currentPage-1)*$numPerPage;
		
		//查询子级会员	
		$sql  ="select * from fly_member where parent_id='$p_id' order by member_id desc limit $beginRecord,$numPerPage";
		$list  =$this->C($this->cacheDir)->findAll($sql);
		foreach($list as $key=>$row){
			$descendant_arr  =$member_tree->get_member_descendant($row['member_id']);//子级会员下所有会员	
			$descendant_txt  =empty($descendant_arr)?0:implode(',',$descendant_arr);
			$team_cnt		  =$this->member_quick_team_cnt($descendant_txt);
			$list[$key]['descendant_txt']	=$descendant_txt;
			$list[$key]['descendant_cnt']	=count($descendant_arr);
			$list[$key]['descendant_money']	=empty($team_cnt['descendant_money'])?0.00:$team_cnt['descendant_money'];
			$list[$key]['descendant_order']	=empty($team_cnt['descendant_order'])?0:$team_cnt['descendant_order'];
			$list[$key]['descendant_one']	=$this->member->member_get_one($row['member_id']);
		}
		if(empty($list)){ $list='null';}
		$rtnArr=array('list'=>$list);
		return $rtnArr;		
	}
	
	public function member_quick_team_json(){
		$assArr  = $this->member_quick_team();
		$rtnArr  =array('code'=>'sucess','message'=>'加载数据','list'=>$assArr['list']);
		echo json_encode($rtnArr);
	}	
	public function member_quick_team_show(){
			$assArr  = $this->member_quick_team();
			$smarty  = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('home/member_quick_team_show.html');	
	}
	
	//统计会员订单总金额 
	public function member_quick_team_cnt($member_txt){
		$sql="select sum(money_dist) as descendant_money,count(id) as descendant_order  from  fly_member_quick where id in($member_txt)";
		$one=$this->C($this->cacheDir)->findOne($sql);
		return $one;
	}
	
}//
?>