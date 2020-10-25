<?php
/*
 *
 * erp.FinFlowRecord  财务流水记录   
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
class FinFlowRecord extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/sysmanage/Auth');
		$this->sys_user=_instance('Action/sysmanage/User');
		$this->bank=_instance('Action/erp/FinBankAccount');
		$this->link_business=_instance('Action/erp/FinFlowLinkBusiness');
		$this->comm=_instance('Extend/Common');
	}	
	
	public function fin_flow_record(){
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
		

		$bank_id		= $this->_REQUEST("bank_id");
		$create_date	= $this->_REQUEST("create_date");
		$where_str	= " create_user_id in (".SYS_USER_SUB_ID.",".SYS_USER_ID.")";
		
		if($bank_id){
			$where_str .=" and bank_id='$bank_id'";
		}
		
		//到期时间
		if( !empty($create_date) ){
			$date_range=$this->comm->date_range('-1',$create_date);
			$where_str .=" and create_time>'$date_range'";
		}
		//排序操作
		$orderField = $this->_REQUEST("orderField");
		$orderDirection = $this->_REQUEST("orderDirection");
		$order_by="order by";
		if( $orderField=='by_rece' ){
			$order_by .=" rece_money $orderDirection";
		}else if($orderField=='by_pay'){
			$order_by .=" pay_money $orderDirection";
		}else if($orderField=='by_balance'){
			$order_by .=" balace $orderDirection";
		}else{
			$order_by .=" id desc";
		}		
		
		$totalSql	 = "select sum(pay_money) as total_pay_money,sum(rece_money) as total_rece_money from fin_flow_record 
						where $where_str ";
		$totalRs	 = $this->C($this->cacheDir)->findOne($totalSql);
		
		$countSql   = "select * from fin_flow_record where $where_str ";
		$totalCount	 = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord= ($pageNum-1)*$pageSize;
		$sql		 = "select * from fin_flow_record where $where_str $order_by limit $beginRecord,$pageSize";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		if(is_array($list)){
			foreach($list as $key=>$row){
				$list[$key]["blank_arr"] = $this->bank->fin_bank_accoun_get_one($row['bank_id']);
				$list[$key]['create_user_arr']	=$this->sys_user->user_get_one($row['create_user_id']);
				$list[$key]["business"] 	= $this->link_business->fin_flow_link_bus($row['bus_id'],$row['bus_type']);
			}
		}
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum,"countMoney"=>$totalRs);		
		return $assignArray;
	}
	
	public function fin_flow_record_json(){
		$assArr = $this->fin_flow_record();
		echo json_encode($assArr);
	}
	
	//显示流水财务
	public function fin_flow_record_show(){
		$bank	 = $this->bank->fin_bank_accoun_select();
		$smarty  = $this->setSmarty();
		$smarty->assign(array('bank'=>$bank));
		$smarty->display('erp/fin_flow_record_show.html');	
	}		

	//流水财务增加函数
	public function fin_flow_record_add($mode='rece',$money=100,$bank_id=1,$bus_id='',$bus_type=''){
		$sql="select balance from fin_flow_record where bank_id='$bank_id' order by id desc";
		$one=$this->C($this->cacheDir)->findOne($sql);
		$balance=empty($one)?'0':$one['balance'];
		if($mode=="pay"){//支出
			$balance=$balance-$money;
			$usql="insert into fin_flow_record(bank_id,pay_money,balance,bus_id,bus_type,create_time,create_user_id) 
								values('$bank_id','$money','$balance','$bus_id','$bus_type','".NOWTIME."','".SYS_USER_ID."');";							
		}elseif($mode=="rece"){
			$balance=$balance+$money;
			$usql="insert into fin_flow_record(bank_id,rece_money,balance,bus_id,bus_type,create_time,create_user_id) 
								values('$bank_id','$money','$balance','$bus_id','$bus_type','".NOWTIME."','".SYS_USER_ID."');";		
		}
		$flow_id=$this->C($this->cacheDir)->update($usql);
		return $flow_id;
	}

    public function fin_flow_record_export(){
        $bank	 = $this->bank->fin_bank_accoun_select();
        $smarty  = $this->setSmarty();
        $smarty->assign(array('bank'=>$bank));
        $smarty->display('erp/fin_flow_record_export.html');

    }

    /**流水导出
     * Author: lingqifei created by at 2020/5/16 0016
     */
    public function fin_flow_record_export_cvs(){

        $bank_id		= $this->_REQUEST("bank_id");
        $create_date	= $this->_REQUEST("create_date");
        $where_str	= " create_user_id>0 ";

        if($bank_id){
            $where_str .=" and bank_id='$bank_id'";
        }
        //到期时间
        if( !empty($create_date) ){
            $date_range=$this->comm->date_range('-1',$create_date);
            $where_str .=" and create_time>'$date_range'";
        }
        //排序操作
        $orderField = $this->_REQUEST("orderField");
        $orderDirection = $this->_REQUEST("orderDirection");
        $order_by="order by";
        if( $orderField=='by_rece' ){
            $order_by .=" rece_money $orderDirection";
        }else if($orderField=='by_pay'){
            $order_by .=" pay_money $orderDirection";
        }else if($orderField=='by_balance'){
            $order_by .=" balace $orderDirection";
        }else{
            $order_by .=" id desc";
        }

        $sql		 = "select * from fin_flow_record where $where_str $order_by";
        $list		 = $this->C($this->cacheDir)->findAll($sql);

        $body_cel=array();
        if(is_array($list)){
            foreach($list as $key=>$row){
                $bank=$this->bank->fin_bank_accoun_get_one($row['bank_id']);
                $business	= $this->link_business->fin_flow_link_bus($row['bus_id'],$row['bus_type']);
                $tmp=array(
                        $bank['name'],
                        $bank['card']."\t",
                        $row['rece_money'],
                        $row['pay_money'],
                        $row['balance'],
                        $business['name'],
                        $business['title'],
                );
                $body_cel[]=$tmp;
            }
        }
        $title_cel=array('银行名称','银行帐号','收入','支出','余额','类型','关联单号');
        export_to_cvs('财务流水'.time().'.csv',$title_cel,$body_cel);
        exit;

    }
}//
?>