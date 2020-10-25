<?php
class WxMemberRank extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		$this->auth=_instance('Action/home/WxAuth');
	}	
	public function member_rank(){
		//**获得传送来的数据作分页处理
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = 10;//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		$beginRecord = ($currentPage-1)*$numPerPage;
		
		$member = $this->L( 'home/WxMember' )->member_get_info();
		$member_id = $member[ 'member_id' ];
		
		//当前会员排名
		$sql="select count(a.member_id) as rank from fly_member a where a.balance>(select b.balance from fly_member b where b.member_id='$member_id')";
		$one= $this->C( $this->cacheDir )->findOne( $sql );
		$member_rank=$one['rank'];
		
		//查出最前面10条会员
		$sql 	= "select * from fly_member order by balance desc limit $beginRecord,$numPerPage";
		$list = $this->C( $this->cacheDir )->findAll( $sql );
		
		foreach ( $list as $key => $row ) {
			$list[ $key ] = $this->L('home/WxMember')->member_get_one($row['member_id']);
		}
		if(empty($list)){ $list='null';}
		$rtnArr=array('list'=>$list,'member_rank'=>$member_rank);
		return $rtnArr;
		
	}
	public function member_rank_show(){
		$assArr  = $this->member_rank();
		$smarty  = $this->setSmarty();
		$smarty->assign($assArr);
		$smarty->display('home/member_rank_show.html');	
	}

}//
?>