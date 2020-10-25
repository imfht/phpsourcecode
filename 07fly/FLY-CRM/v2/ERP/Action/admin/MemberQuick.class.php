<?php
/*
 *
 * admin.MemberQuick 会员佣金记录，主要管理用户的每一单的返点金额以及下级的分成
 *
 * =========================================================
 * 零起飞网络 - 专注于网站建设服务和行业系统开发
 * 以质量求生存，以服务谋发展，以信誉创品牌 !
 * ----------------------------------------------
 * @copyright	Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license    For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author ：kfrs <goodkfrs@QQ.com> 574249366
 * @version ：1.0
 * @link ：http://www.07fly.top 
 */	
class MemberQuick extends Action{
	private $cacheDir='';//缓存目录
	public function __construct() {
		/*$this->auth=_instance('Action/sysmanage/Auth');*/
	}	
	public function member_quick(){
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
		
		//**************************************************************************
		//**获得传送来的数据做条件来查询
		$dist_account	 = $this->_REQUEST("dist_account");
		$sale_account	 = $this->_REQUEST("sale_account");
		$name	 = $this->_REQUEST("name");
		$sdt1   	= $this->_REQUEST("sdt1");
		$edt1   	= $this->_REQUEST("edt1");
		$where_str = " q.id != 0";
		if( !empty($name) ){
			$where_str .=" and q.name like '%$name%'";
		}
		if( !empty($dist_account) ){
			$where_str .=" and m1.account like '%$dist_account%'";
		}
		if( !empty($sale_account) ){
			$where_str .=" and m2.account like '%$sale_account%'";
		}
		if( !empty($sdt1) ){
			$where_str .=" and q.adt >= '$sdt1'";
		}			
		if( !empty($edt1) ){
			$where_str .=" and q.adt < '$edt1'";
		}
		
		//排序生成
		$orderField = $this->_REQUEST("orderField");
		$orderDirection = $this->_REQUEST("orderDirection");
		$order_by="order by";
		if( $orderField=='by_adt' ){
			$order_by .=" adt $orderDirection";
		}else{
			$order_by .=" id desc";
		}
		
		//**************************************************************************统计总积分，
		$sumSql	="select sum(q.money) as all_money, sum(q.integral) as all_integral from fly_member_quick as q 		
				 where $where_str";
		$sumOne	= $this->C($this->cacheDir)->findOne($sumSql);
		//**************************************************************************
		$countSql  = "select * from fly_member_quick as q 
						where $where_str";
		$totalCount = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord= ($pageNum-1)*$pageSize;//计算开始行数
		
		$sql		 = "select q.* from fly_member_quick as q 				
						where $where_str $order_by limit $beginRecord,$pageSize";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$mem =$this->L('admin/Member');
		foreach($list as $key=>$row){
			$list[$key]['quick_member_name']  =$mem->member_get_name($row['quick_member_id']);
			$list[$key]['buyer_member_name']  =$mem->member_get_name($row['buyer_member_id']);
		}
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);	
		return $assignArray;
		
	}
	public function member_quick_json(){
		$assArr  = $this->member_quick();
		echo json_encode($assArr);
	}		
	public function member_quick_show(){
			$assArr  = $this->member_quick();
			$smarty  = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('admin/member_quick_show.html');	
	}

	public function member_quick_add(){
		$id	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$smarty = $this->setSmarty();
			$smarty->assign(array('id'=>$id));
			$smarty->display('admin/member_quick_add.html');	
		}else{
			$this->member_quick_add_save($id);
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功");		
		}
	}		
	
	//每天执行之后就生成一条记录
	public function member_quick_add_save($member_id){
		$dt	   		= date("Y-m-d H:i:s",time());
		$money 	 = $this->_REQUEST("money");
		
		$sql="select * from fly_member where id='$member_id'";
		$one= $this->C($this->cacheDir)->findOne($sql);
		$member_money=$one['money'];
		
		//查询交换比例
		$day_sql="select * from fly_conf_day limit 0,1";
		$day_cfg=$this->C($this->cacheDir)->findOne($day_sql);

		$day_integral=$member_money*$day_cfg['rate'];
		//开启事务
		$this->C($this->cacheDir)->begintrans();
		//插入兑换记录
		$sql="insert into fly_member_quick(member_id,money,integral,adt) 
								values('$member_id','$day_integral','-$day_integral','$dt')";
		$rtn=$this->C($this->cacheDir)->update($sql);
		if($rtn>0){
			$this->C($this->cacheDir)->rollback();
		}
		//更新会员余额和积分
		$u_sql="update fly_member set money=money+$day_integral,integral=integral-$day_integral where id='$member_id'";
		$u_rtn=$this->C($this->cacheDir)->update($u_sql);
		if($u_rtn>0){
			$this->C($this->cacheDir)->rollback();
		}
		//事件提交
		$this->C($this->cacheDir)->commit();
		
		return $day_integral;
	}
		
	public function member_quick_del(){
		$id	  = $this->_REQUEST("id");
		$sql  = "delete from fly_member_quick where id in ($id)";
		$this->C($this->cacheDir)->update($sql);		
		$rtnArr=array('rtnstatus'=>'success','msg'=>'删除成功');
		echo json_encode($rtnArr);
	}	
	
}//
?>