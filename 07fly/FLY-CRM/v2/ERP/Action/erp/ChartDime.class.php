<?php	 
/*
 * epr.ChartDime  按销客户总统计
 *
 * @copyright   Copyright (C) 2017-2028 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class ChartDime extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/sysmanage/Auth');
		$this->comm=_instance('Extend/Common');
	}
	
	
	public function chart_dime_show(){
		
		$create_date_b	= $this->_REQUEST("create_date_b");		
		$create_date_e	= $this->_REQUEST("create_date_e");

        $customer['all']	=$this->chart_customer();
        $customer['today']	=$this->chart_customer(array('today'=>1));
        $customer['seven']	=$this->chart_customer(array('dt'=>'7d'));
        $customer['month']	=$this->chart_customer(array('dt'=>'1m'));


        $trace['all']		=$this->chart_trace();
        $trace['today']		=$this->chart_trace(array('today'=>1));
        $trace['seven']		=$this->chart_trace(array('dt'=>'7d'));
        $trace['month']		=$this->chart_trace(array('dt'=>'1m'));

        $contract['all']		=$this->chart_contract();
        $contract['today']		=$this->chart_contract(array('today'=>1));
        $contract['seven']		=$this->chart_contract(array('dt'=>'7d'));
        $contract['month']		=$this->chart_contract(array('dt'=>'1m'));

        $rtnArr	=array('customer'=>$customer,'trace'=>$trace,'contract'=>$contract);
        $smarty	=$this->setSmarty();
        $smarty->assign($rtnArr);
		$smarty->display('erp/chart_dime_show.html');	
	}
	
	//客户统计
	public function chart_customer($arr=array()){
		
		$create_date_b	= $this->_REQUEST("create_date_b");		
		$create_date_e	= $this->_REQUEST("create_date_e");		
		$where_str	= " owner_user_id in (".SYS_USER_ID.",".SYS_USER_SUB_ID.")";
		if(isset($arr['dt'])){
			$date_range=$this->comm->date_range('-1',$arr['dt']);
			$where_str .=" and create_time>'$date_range'";			
		}
		if(isset($arr['today'])){
			$where_str .=" and to_days(create_time)=to_days(now())";
		}
		//创建日期
		if( !empty($create_date_b) ){
			$where_str .=" and create_time>='$create_date_b'";
		}		
		if( !empty($create_date_e) ){
			$where_str .=" and create_time<'$create_date_b'";
		}
		
		$sql   = "SELECT count(1) as total_num FROM cst_customer WHERE $where_str";
		$one	= $this->C($this->cacheDir)->findOne($sql);
		foreach($one as $key=>$val){
			if(empty($val)) $one[$key]=0;
		}
		return $one;
		
	}

	//客户跟单统计
	public function chart_trace($arr=array()){
		$where_str	= " create_user_id in (".SYS_USER_ID.",".SYS_USER_SUB_ID.")";
		$create_date_b	= $this->_REQUEST("create_date_b");		
		$create_date_e	= $this->_REQUEST("create_date_e");		
		if(isset($arr['dt'])){
			$date_range=$this->comm->date_range('-1',$arr['dt']);
			$where_str .=" and create_time>'$date_range'";			
		}
		if(isset($arr['today'])){
			$where_str .=" and to_days(create_time)=to_days(now())";
		}
		//创建日期
		if( !empty($create_date_b) ){
			$where_str .=" and create_time>='$create_date_b'";
		}		
		if( !empty($create_date_e) ){
			$where_str .=" and create_time<'$create_date_b'";
		}
		
		$sql   = "SELECT count(1) as total_num 	FROM cst_trace 	WHERE $where_str";
		$one	= $this->C($this->cacheDir)->findOne($sql);
		foreach($one as $key=>$val){
			if(empty($val)) $one[$key]=0;
		}
		return $one;
	}
    //合同统计
    public function chart_contract($arr=array()){
        $where_str	= " create_user_id in (".SYS_USER_ID.",".SYS_USER_SUB_ID.")";

        if(isset($arr['dt'])){
            $date_range=$this->comm->date_range('-1',$arr['dt']);
            $where_str .=" and create_time>'$date_range'";
        }
        if(isset($arr['today'])){
            $where_str .=" and create_time='".NOWTIME."'";
        }

        $sql   = "SELECT count(1) as total_num,
						sum(money) as total_money,sum(back_money) as total_back_money,sum(owe_money) as total_owe_money,
						sum(deliver_money) as total_deliver_money,sum(zero_money) as total_zero_money,sum(invoice_money) as total_invoice_money
						FROM sal_contract 
					    WHERE $where_str";
        $one	= $this->C($this->cacheDir)->findOne($sql);
        foreach($one as $key=>$val){
            if(empty($val)) $one[$key]=0;
        }
        return $one;
    }
	
}//
?>