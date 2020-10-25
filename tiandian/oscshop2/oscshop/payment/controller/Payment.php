<?php
/**
 * oscshop2 B2C电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015-2016 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 * 电脑版本
 */
 
namespace osc\payment\controller;
use osc\common\controller\Base;
use think\Db;
class Payment extends Base{

	
	function pay_api(){
		if(request()->isPost()){
		
			$type=session('payment_method');
			
			$class = '\\osc\\payment\\controller\\' . ucwords($type);
				
			$payment= new $class();
			
			storage_user_action(member('uid'),member('username'),config('FRONTEND_USER'),'下了订单，未支付');	
			
			$url=$payment->process();
			
			return $url;
		
		}
	}
	
	function choice_payment_type(){
		
		$map['order_id']=['eq',(int)input('param.order_id')];
		$map['uid']=['eq',member('uid')];
		
		if(!$order=Db::name('order')->where($map)->find()){
			$this->error('订单不存在！！');
		}
		
		session('re_pay_order_id',$order['order_id']);
		
		$this->assign('list',osc_service('payment','service')->get_available_payment_list());
		
		return $this->fetch('payment_list'); 
	}
	function re_pay(){
		if(request()->isPost()){
		
			$type=input('param.type');
			
			$class = '\\osc\\payment\\controller\\' . ucwords($type);
				
			$payment= new $class();
			
			$return=$payment->re_pay((int)session('re_pay_order_id'));
			
			storage_user_action(member('uid'),member('username'),config('FRONTEND_USER'),'点击了去支付');
			
			return ['type'=>$return['type'],'pay_url'=>$return['pay_url']];
		
		}
	}
}
