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
 * 扫码支付
 */
namespace osc\payment\controller;
use osc\common\controller\Base;
use payment\weixin\WxPayApi;
use payment\weixin\WxPayConfig;
use payment\weixin\WxPayUnifiedOrder;
use payment\weixin\WxPayNotifyCallBack;
use think\Db;

class Weixin extends Base{
	
	function process(){
		return ['type'=>'wx_pay','url'=>url('/wxpay')];
	}
	
	public function re_pay($order_id){
		return ['type'=>'wx_pay','pay_url'=>url('payment/weixin/re_pay_code',array('order_id'=>$order_id))];
	}
	
	function code(){
		
		$order=osc_order()->add_order('weixin');
		
		if($order['order_id']){
		
			$config=payment_config('weixin');
			
			$cfg = array(
			    'APPID'     => $config['appid'],
			    'MCHID'     => $config['weixin_partner'],
			    'KEY'       => $config['partnerkey'],
			    'APPSECRET' => $config['appsecret'],
			    'NOTIFY_URL' =>request()->domain().url('payment/weixin/weixin_notify')
		    );
		    WxPayConfig::setConfig($cfg);     
	        //②、统一下单
	        $input = new WxPayUnifiedOrder();           
	  
	        $input->SetBody($order['subject']);
	        $input->SetAttach('附加数据');
	        $input->SetOut_trade_no($order['pay_order_no']);
			
	        $input->SetTotal_fee((float)$order['pay_total']*100);
			
	        $input->SetTime_start(date("YmdHis"));
	        $input->SetTime_expire(date("YmdHis", time() + 600));
			$input->SetTrade_type('NATIVE');
	
			$input->SetProduct_id(time());
			
			$wxapi=new WxPayApi();
			
		    $url= $wxapi->unifiedOrder($input);	
		
			osc_order()->clear_cart($order['uid']);
			
			$this->assign('url',$url['code_url']);
			
			$this->assign('trade_no',$order['pay_order_no']);
			
		}
		
		return $this->fetch(); 
	}
	//会员中心去支付
	public function re_pay_code(){
		
		$order_id=(int)input('order_id');
		
		$order=Db::name('order')->where('order_id',$order_id)->find();
		
		if($order&&($order['order_status_id']!=config('paid_order_status_id'))){
		
			$config=payment_config('weixin');
	
			$cfg = array(
			    'APPID'     => $config['appid'],
			    'MCHID'     => $config['weixin_partner'],
			    'KEY'       => $config['partnerkey'],
			    'APPSECRET' => $config['appsecret'],
			    'NOTIFY_URL' =>request()->domain().url('payment/weixin/weixin_notify')
		    );
			//重新生成trade_no
			$trade_no=build_order_no();
			
			Db::name('order')->where('order_id',$order['order_id'])->update(array('order_num_alias'=>$trade_no,'payment_code'=>'weixin'));	
			
		    WxPayConfig::setConfig($cfg);     
	        //②、统一下单
	        $input = new WxPayUnifiedOrder();           
	  
	        $input->SetBody($order['pay_subject']);
	        $input->SetAttach('附加数据');
	        $input->SetOut_trade_no($trade_no);
			
	        $input->SetTotal_fee((float)$order['total']*100);
			
	        $input->SetTime_start(date("YmdHis"));
	        $input->SetTime_expire(date("YmdHis", time() + 600));
			$input->SetTrade_type('NATIVE');
	
			$input->SetProduct_id(time());
			
			$wxapi=new WxPayApi();
			
		    $url= $wxapi->unifiedOrder($input);				
			
			$this->assign('url',$url['code_url']);
			
			$this->assign('trade_no',$trade_no);
			
			$this->assign('order_id',$order_id);
			
			return $this->fetch('recode'); 
		}
	}
	public function get_order_status(){
		
		$data=input('post.');
		
		$order=Db::name('order')->where('order_num_alias',$data['out_trade_no'])->find();	
		
		if($order['order_status_id']==config('paid_order_status_id')){
			die('pay_suc');
		}else{
			die('no_pay');
		}
	}
	//异步通知
	public function weixin_notify(){	
		
		$config=payment_config('weixin');
		
		$notify_url=request()->domain().url('payment/weixin/weixin_notify');
		
		$cfg = array(
			'APPID'     => $config['appid'],
			'MCHID'     => $config['weixin_partner'],
			'KEY'       => $config['partnerkey'],
			'APPSECRET' => $config['appsecret'],
			'NOTIFY_URL' => $notify_url,
		);
		WxPayConfig::setConfig($cfg); 	

		$call_back=new WxPayNotifyCallBack();
		
		$data=$call_back->Handle(false);
		
		if($data&&$data['result_code']=='SUCCESS'){
			
			$order=Db::name('order')->where('order_num_alias',$data['out_trade_no'])->find();		
			
			if($order&&($order['order_status_id']==config('default_order_status_id'))){				
				osc_order()->update_order($order['order_id']);					
			}
			
            echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        }else{
            echo '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[签名失败]]></return_msg></xml>';
        }
		
	}
}
