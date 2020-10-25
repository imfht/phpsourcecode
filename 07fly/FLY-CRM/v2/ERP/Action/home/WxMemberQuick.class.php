<?php
/*
 * 产品管理类
 */	
class WxMemberQuick extends Action{	
	private $cacheDir='';//缓存目录
	private $type='';//缓存目录
	private $member='';//缓存目录
	private $shop='';//缓存目录
	public function __construct() {
		$this->auth=_instance('Action/home/WxAuth');		
		$this->member=_instance('Action/home/WxMember');		
	}
	
	//佣金明细
	public function member_quick(){
		
		$member		 =$this->L('home/WxMember')->member_get_info();
		$member_id  =$member['member_id'];
		
		//**获得传送来的数据作分页处理
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = 10;//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		
		//**************************************************************************
		//**获得传送来的数据做条件来查询
		$name	   = $this->_REQUEST("name");
		$typeid	   = $this->_REQUEST("typeid");
		$where_str = " q.quick_member_id='$member_id' ";

		//**************************************************************************
		$countSql    = "select q.id from fly_member_quick as q left join fly_goods_order as o on q.goods_order_id=o.order_id 
							where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select q.*,o.order_no
						from fly_member_quick as q left join fly_goods_order as o on q.goods_order_id=o.order_id  
						where $where_str order by q.id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$ifpay_status=$this->member_quick_ifpay_status();
		foreach($list as $key=>$row){
			$list[$key]['quick_member_name']=$this->member->member_get_one($row['quick_member_id']);
			$list[$key]['ifpay_name']=$ifpay_status[$row['ifpay']];
		}		
		if(empty($list)){ $list='null';}
		$typelist	 =$this->L('home/WxShopType')->shop_type();
		$assignArray = array('list'=>$list,"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage,
							"name"=>$name,"typelist"=>$typelist,
							);	
		return $assignArray;
		
	}
	public function member_quick_json(){
		$assArr  = $this->member_quick();
		$rtnArr  =array('code'=>'sucess','message'=>'加载数据','list'=>$assArr['list']);
		echo json_encode($rtnArr);
	}	
	public function member_quick_show(){
			$assArr  = $this->member_quick();
			$smarty  = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('home/member_quick_show.html');	
	}

	public function member_quick_ifpay_status(){
		$rtn=array(
			"0"=>"未返还",
			"1"=>"已返还"
		);
		return $rtn;
	}
}//
?>