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
 */
 
namespace osc\payment\controller;
use osc\common\controller\Base;
use think\Db;
class Alipay extends Base{
	
	//下单处理
	public function process(){
		
		return ['url'=>$this->alipay_url(osc_order()->add_order('alipay'))];
	}
	public function alipay_url($order,$type=''){		
		
		if($order['order_id']){
			
			$payment=payment_config('alipay');
			
			$payment['notify_url']=request()->domain().url('payment/alipay/alipay_notify');					
					
			$payment['return_url']=request()->domain().url('payment/alipay/alipay_return');//同步通知
			$payment['order_type']='goods_buy';
			$payment['subject']=$order['subject'];
			$payment['name']=$order['name'];
			$payment['pay_order_no']=$order['pay_order_no'];
			$payment['pay_total']=$order['pay_total'];					

			$alipay= new \payment\alipay\Alipay($payment);
			
			$url= $alipay->get_payurl();
			
			if($type=='re_pay'){
				session('re_pay_order_id',null);
			}else{
				osc_order()->clear_cart($order['uid']);
			}
			
			return $url;
		}
		
		
	}
	public function re_pay($order_id){

		$order=Db::name('order')->where('order_id',(int)$order_id)->find();
		
		if($order&&($order['order_status_id']!=config('paid_order_status_id'))){
			$url=$this->alipay_url([
				'order_id'=>$order['order_id'],
				'subject'=>$order['pay_subject'],
				'name'=>$order['name'],
				'pay_order_no'=>$order['order_num_alias'],
				'pay_total'=>$order['total'],
				'uid'=>$order['uid'],
			],'re_pay'
			);
		}					
		return ['type'=>'alipay','pay_url'=>$url];
	}
	
	//异步通知
	public function alipay_notify(){
	
		
		$alipay= new \payment\alipay\Alipay(payment_config('alipay'));	
		
		$verify_result = $alipay->verifyNotify();
		
		if($verify_result) {		
			
			$post=input('post.');
			
			$order=Db::name('order')->where('order_num_alias',$post['out_trade_no'])->find();
			
			if($post['trade_status'] == 'TRADE_FINISHED') {				
				
		    }
		    elseif($post['trade_status'] == 'TRADE_SUCCESS') {		
				
				if($order&&($order['order_status_id']!=config('paid_order_status_id'))){
										
					osc_order()->update_order($order['order_id']);
					
					echo "success";		
									
				}else{
					echo "fail";
				}		        
				
		    }			
			
		}else{
			
			echo "fail";
		}
	}
	//同步通知
	public function alipay_return(){
		
		$alipay= new \payment\alipay\Alipay(payment_config('alipay'));		
		//对进入的参数进行远程数据判断
		$verify = $alipay->return_verify();
	
		if($verify){
		
			$get=input('param.');
			
			$order=Db::name('order')->where('order_num_alias',$get['out_trade_no'])->find();
			
			if($order['order_status_id']==config('paid_order_status_id')){
				@header("Location: ".url('/pay_success'));	
				die;
			}
			
			if($order&&($order['order_status_id']!=config('paid_order_status_id'))){
				//支付完成
				if($get['trade_status']=='TRADE_SUCCESS'){					
					
					osc_order()->update_order($order['order_id']);
					
					@header("Location: ".url('/pay_success'));	
				}						
			}else{
				die('订单不存在');
			}
			
		}else{
			die('支付失败');
		}	
	}
	
}
