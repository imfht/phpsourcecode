<?php
/*
 * 财务流水和业务订单关联关系类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class FinFlowLinkBusiness extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/sysmanage/Auth');
	}	
	
	public function fin_flow_link_bus($bus_id,$bus_type){
		$rtnArr=array();
		switch($bus_type){
			case "sal_contract"://合同收入
				$sql="select * from sal_contract where  contract_id='$bus_id'";
				$one=$this->C($this->cacheDir)->findOne($sql);
				$rtnArr=array(
					'name'=>'销售合同',
					'name_html'=>'<span class="label label-info">销售合同<span>',
					'title'=>$one["title"],
					'bus_id'=>$bus_id,
					'bus_type'=>$bus_type,
				);
				break;
			case "pos_contract"://采购单号
				$sql="select * from pos_contract where  contract_id='$bus_id'";
				$one=$this->C($this->cacheDir)->findOne($sql);
				$rtnArr=array(
					'name'=>'采购合同',
					'name_html'=>'<span class="label label-primary">采购合同<span>',
					'title'=>$one["title"],
					'bus_id'=>$bus_id,
					'bus_type'=>$bus_type,
				);
				break;					
	
			case "fin_income_record"://其他收入
				$sql="select * from fin_income_record where record_id='$bus_id'";
				$one=$this->C($this->cacheDir)->findOne($sql);
				$rtnArr=array(
					'name'=>'其他收入',
					'name_html'=>'<span class="label label-info">其他收入<span>',
					'title'=>'其他收入-'.$one["record_id"],
					'bus_id'=>$bus_id,
					'bus_type'=>$bus_type,
				);
				break;
			case "fin_expenses_record"://其他支出
				$sql="select * from fin_expenses_record where  record_id='$bus_id'";
				$one=$this->C($this->cacheDir)->findOne($sql);
				$rtnArr=array(
					'name'=>'其他支出',
					'name_html'=>'<span class="label label-primary">其他支出<span>',
					'title'=>'其他支出-'.$one["record_id"],
					'bus_id'=>$bus_id,
					'bus_type'=>$bus_type,
				);
				break;
			case "fin_capital_record"://资金注入抽取
				$sql="select * from fin_capital_record where  record_id='$bus_id'";
				$one=$this->C($this->cacheDir)->findOne($sql);
				if($one['type_id']=='1'){
					$rtnArr=array(
						'name'=>'资金注入',
						'name_html'=>'<span class="label label-primary">资金注入<span>',
						'title'=>'资金注入-'.$one["record_id"],
						'bus_id'=>$bus_id,
						'bus_type'=>$bus_type,
					);					
				}else if($one['type_id']=='-1'){
					$rtnArr=array(
						'name'=>'资金抽取',
						'name_html'=>'<span class="label label-primary">资金抽取<span>',
						'title'=>'资金抽取-'.$one["record_id"],
						'bus_id'=>$bus_id,
						'bus_type'=>$bus_type,
					);						
				}
				break;	
			default :
				$rtnArr=array(
					'name'=>'无类型',
					'title'=>'无联单号',
					'bus_id'=>$bus_id,
					'bus_type'=>$bus_type,
				);
				break;
		}
		
		if(empty($rtnArr)){
			$rtnArr=array(
				'name'=>'无类型',
				'name_html'=>'<span class="label label-danger">无联单号<span>',
				'title'=>'无联单号',
				'bus_id'=>$bus_id,
				'bus_type'=>$bus_type,
			);			
		}
		
		return $rtnArr;
			
	}	
	
}//
?>