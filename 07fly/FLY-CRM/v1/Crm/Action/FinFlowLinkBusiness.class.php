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
		_instance('Action/Auth');
	}	
	
	public function fin_flow_link_bus($busID,$busType){
		switch($busType){
			case "sal_contract"://合同收入
				$sql="select * from sal_contract where  id='$busID'";
				$one=$this->C($this->cacheDir)->findOne($sql);
				$url="<a href='".ACT."/SalContract/sal_contract_show_one/id/".$one["id"]."/' target='dialog' width='880' height='480'>销售合同：".$one["con_number"]."</a>";
				break;
				
			case "sal_order"://订单收入
				$sql="select * from sal_order where  id='$busID'";
				$one=$this->C($this->cacheDir)->findOne($sql);
				$url="<a href='".ACT."/SalOrder/sal_order_show_one/id/".$one["id"]."/' target='dialog' width='880' height='480'>销售订单：".$one["ord_number"]."</a>";
				break;				

			case "pos_order"://采购单号
				$sql="select * from pos_order where  id='$busID'";
				$one=$this->C($this->cacheDir)->findOne($sql);
				$url="<a href='".ACT."/PosOrder/pos_order_show_one/id/".$one["id"]."/' target='dialog' width='880' height='480'>采购订单：".$one["pos_number"]."</a>";
				break;					
	
			case "fin_income_record"://其实收入
				$sql="select * from fin_income_record where  id='$busID'";
				$one=$this->C($this->cacheDir)->findOne($sql);
				$url="<a href='".ACT."/FinIncomeRecord/fin_income_record_view/id/".$one['id']."' target='dialog'>其它收入</a>";
				break;
			case "fin_expenses_record"://其实支出
				$sql="select * from fin_expenses_record where  id='$busID'";
				$one=$this->C($this->cacheDir)->findOne($sql);
				$url="<a href='".ACT."/FinExpensesRecord/fin_expenses_record_view/id/".$one['id']."' target='dialog'>其它支出</a>";
				break;				
			default :
				$url="无关联号";
				break;
		}
		return $url;
			
	}	
	
}//
?>