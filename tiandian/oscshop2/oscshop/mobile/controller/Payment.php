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
 
namespace osc\mobile\controller;
use osc\common\controller\Base;
use \think\Db;
use wechat\Curl;
class Payment extends Base{
	
	//积分兑换
	public function points_pay(){
		if(request()->isPost()){
		
			$result=$this->validate_pay('points');
			
			if(isset($result['error'])){
				return $result;
			}
			
			$cart=osc_cart();
			//兑换所需积分
			$pay_points=$cart->get_pay_points($result['uid'],'points');
			
			if(user('points')<$pay_points){
				return ['error'=>'积分不足，剩余积分'.user('points')];
			}
			
			//需要配送的,积分兑换都不需要运费
			if($result['shipping']){							
				$order['shipping_method']=config('default_transport_id');			
			}else{				
				$order['shipping_method']='';
			}
	
			$order['shipping_address_id']=input('post.address_id');
			
			$order['payment_method']='points';
			$order['weight']=0;
			$order['shipping_city_id']=$result['city_id'];
			$order['comment']=input('post.comment');
			$order['uid']=$result['uid'];
			$order['type']='points';
			
			//写入订单
			$_order=osc_order()->add_order('points',$order);			
			//积分
			Db::name('member')->where('uid',$result['uid'])->setDec('points',$pay_points);	//剩余
			Db::name('member')->where('uid',$result['uid'])->setInc('cash_points',$pay_points);	//已经兑换
			//写入积分记录
			Db::name('points')->insert([
				'uid'=>$result['uid'],
				'order_id'=>$_order['order_id'],
				'order_num_alias'=>$_order['pay_order_no'],
				'points'=>$pay_points,
				'description'=>'积分兑换',
				'prefix'=>2,
				'creat_time'=>time(),
				'type'=>1				
			]);	
			//清空购物车
			Db::name('cart')->where(array('uid'=>$result['uid'],'type'=>'points'))->delete();
			//清空购物车
			osc_order()->update_order($_order['order_id']);
			
			return ['success'=>1];			
		}	
	}

	//清空购物车
	private function clear_cart($uid,$type='money'){
		Db::name('cart')->where(array('uid'=>$uid,'type'=>$type))->delete();
		session('mobile_total',null);
	}
	
	//数据验证
	private function validate_pay($type='money'){
		
		$uid=user('uid');
		
		if(!$uid){
			return ['error'=>'请先登录'];
		}
		
		$cart=osc_cart();
		
		if(!$cart->count_cart_total($uid,$type)) {	
			return ['error'=>'您的购物车没有商品'];	
		}
		
		$city_id=input('post.city_id');		
		
		$shipping=$cart->has_shipping(user('uid'),$type);
		//配送验证
		if(isset($shipping['error'])){			
			return ['error'=>$shipping['error']];
		}
		
		//需要配送的
		if($shipping){
			if($city_id==''){
				return ['error'=>'请选择收货地址'];
			}
		}
		
		// 验证商品数量		
		$cart_list=Db::name('cart')->where('uid',$uid)->select();
		
		foreach ($cart_list as $k => $v) {
			
			$param['option']=json_decode($v['goods_option'], true);
			$param['goods_id']=$v['goods_id'];
			$param['quantity']=$v['quantity'];
			
			//判断商品是否存在，验证最小起订量   		
	   		if($error=$cart->check_minimum($param)){   			
	   			return $error;			
	   		}			
					
			//验证商品数量和必选项
			if($error=$cart->check_quantity($param)){			
				return $error;
			}
			
		}
		return [
			'uid'=>$uid,
			'city_id'=>$city_id,
			'shipping'=>$shipping
		];
	}
	//支付宝支付
	function alipay_pay(){
		if(request()->isPost()){
		
			$result=$this->validate_pay();
			
			if(isset($result['error'])){
				return $result;
			}
			
			$cart=osc_cart();
			
			//需要配送的
			if($result['shipping']){								
				$weight = $cart->get_weight($result['uid']);	
				$order['shipping_method']=config('default_transport_id');			
			}else{
				$weight=0;
				$order['shipping_method']='';
			}
	
			$order['shipping_address_id']=input('post.address_id');
			
			$order['payment_method']='alipay';
			$order['weight']=$weight;
			$order['shipping_city_id']=$result['city_id'];
			$order['comment']=input('post.comment');
			$order['uid']=$result['uid'];
		
			$order=osc_order()->add_order('alipay',$order);
			
			$this->clear_cart($order['uid']);
			
			$config=payment_config('alipay');
			
			$alipay_config = array(
					"service"       => 'alipay.wap.create.direct.pay.by.user',
					"partner"       => $config['partner'],
					"seller_id"     => $config['partner'],
					"key"			=> $config['key'],
					"payment_type"	=> 1,
					"notify_url"	=> request()->domain().url('payment/alipay_notify'),
					"return_url"	=> request()->domain().url('payment/alipay_return'),
					"_input_charset"	=> trim(strtolower(strtolower('utf-8'))),
					"out_trade_no"	=> $order['pay_order_no'],
					"subject"	=> $order['subject'],
					"total_fee"	=> $order['pay_total'],
					"show_url"	=> '',
					'transport'=>'http',
					'sign_type'=>strtoupper('MD5'),
					//"app_pay"	=> "Y",//启用此参数能唤起钱包APP支付宝
					"body"	=> '',								
			);
	
			
			$alipay = new \payment\alipay\Alipay($alipay_config,'mobile');
			
			$url = $alipay->get_payurl();
			
			return ['success'=>1,'type'=>'alipay','url'=>$url];
			
		}
	}
	//支付宝异步通知
	function alipay_notify(){
		
		$alipay= new \payment\alipay\Alipay(payment_config('alipay'));	
		
		$verify_result = $alipay->verifyNotify();
		
		if($verify_result) {		
			
			$post=input('post.');
			
			$order=Db::name('order')->where('order_num_alias',$post['out_trade_no'])->find();
			
			if($post['trade_status'] == 'TRADE_FINISHED') {				
				
		    }elseif($post['trade_status'] == 'TRADE_SUCCESS') {		
				
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
	//支付宝同步通知
	function alipay_return(){
		
		$alipay= new \payment\alipay\Alipay(payment_config('alipay'));		
		//对进入的参数进行远程数据判断
		$verify = $alipay->return_verify();
	
		if($verify){
		
			$get=input('param.');
			
			$order=Db::name('order')->where('order_num_alias',$get['out_trade_no'])->find();
			
			if($order['order_status_id']==config('paid_order_status_id')){
				@header("Location: ".url('pay_success/index'));	
				die;
			}
			
			if($order&&($order['order_status_id']!=config('paid_order_status_id'))){
				//支付完成
				if($get['trade_status']=='TRADE_SUCCESS'){					
					
					osc_order()->update_order($order['order_id']);
					
					@header("Location: ".url('pay_success/index'));	
				}						
			}else{
				die('订单不存在');
			}
			
		}else{
			die('支付失败');
		}
	}
	
	//支付宝，我的订单-》立即支付
	function alipay_repay(){
		
		$order_id=(int)input('param.order_id');
		
		$check=osc_order()->check_goods_quantity($order_id);
		
		if(isset($check['error'])){
			return $check;
		}
		
		$order=Db::name('order')->where('order_id',$order_id)->find();
		
		if($order&&($order['order_status_id']!=config('paid_order_status_id'))){		
		
			$config=payment_config('alipay');
				
			$alipay_config = array(
					"service"       => 'alipay.wap.create.direct.pay.by.user',
					"partner"       => $config['partner'],
					"seller_id"     => $config['partner'],
					"key"			=> $config['key'],
					"payment_type"	=> 1,
					"notify_url"	=> request()->domain().url('payment/alipay_notify'),
					"return_url"	=> request()->domain().url('payment/alipay_return'),
					"_input_charset"	=> trim(strtolower(strtolower('utf-8'))),
					"out_trade_no"	=> $order['order_num_alias'],
					"subject"	=> $order['pay_subject'],
					"total_fee"	=> $order['total'],
					"show_url"	=> '',
					'transport'=>'http',
					'sign_type'=>strtoupper('MD5'),
					//"app_pay"	=> "Y",//启用此参数能唤起钱包APP支付宝
					"body"	=> '',								
			);
	
			
			$alipay = new \payment\alipay\Alipay($alipay_config,'mobile');
			
			$url = $alipay->get_payurl();
			
			return ['success'=>1,'url'=>$url];
		}else{
			return ['error'=>'订单已经支付'];
		}
	}
	//微信支付
	function weixin_pay(){
		if(request()->isPost()){
			
			$jssdk_order=session('jssdk_order');
			
			if(!empty($jssdk_order)){
				
				$return=$jssdk_order;
				
			}else{
				
				$result=$this->validate_pay();
				
				if(isset($result['error'])){
					return $result;
				}
				
				$cart=osc_cart();
				
				//需要配送的
				if($result['shipping']){								
					$weight = $cart->get_weight($result['uid']);	
					$order['shipping_method']=config('default_transport_id');			
				}else{
					$weight=0;
					$order['shipping_method']='';
				}
		
				$order['shipping_address_id']=input('post.address_id');
				
				$order['payment_method']='weixin';
				$order['weight']=$weight;
				$order['shipping_city_id']=$result['city_id'];
				$order['comment']=input('post.comment');
				$order['uid']=$result['uid'];
				
				$jssdk_order=session('jssdk_order');			
			
				$return=osc_order()->add_order('weixin',$order);
				
				session('jssdk_order',$return);
				
				$this->clear_cart($return['uid']);
			}				
						
			return $this->getBizPackage($return);
			
		}
	}
	//微信，我的订单-》立即支付
	function weixin_repay(){
		$order_id=(int)input('param.order_id');
		
		$check=osc_order()->check_goods_quantity($order_id);
		
		if(isset($check['error'])){
			return $check;
		}
		
		$order=Db::name('order')->where('order_id',$order_id)->find();
		
		if($order&&($order['order_status_id']!=config('paid_order_status_id'))){
				
			$return['pay_total']=$order['total'];
			$return['subject']=$order['pay_subject'];
			$return['pay_order_no']=$order['order_num_alias'];
			
			return $this->getBizPackage($return);
		}else{
			return ['error'=>'订单已经支付'];
		}
	}

	//微信jssdk回调
	public function jsskd_notify(){
		
		if(wechat()->checkPaySign()){
			
			$sourceStr = file_get_contents('php://input');		
		 
	        // 读取数据
	        $postObj = simplexml_load_string($sourceStr, 'SimpleXMLElement', LIBXML_NOCDATA);
		 
			if (!$postObj) {
	       		 echo "<xml><return_code><![CDATA[FAIL]]></return_code></xml>";
	        } else {
			
				$order=Db::name('order')->where('order_num_alias',$postObj->out_trade_no)->find();		
					
				if($order&&($order['order_status_id']!=config('paid_order_status_id'))){
										
					osc_order()->update_order($order['order_id']);
					
					echo "<xml><return_code><![CDATA[SUCCESS]]></return_code></xml>";	
									
				}else{
					echo "<xml><return_code><![CDATA[FAIL]]></return_code></xml>";
				}	
				
	        }			
			
		}else{  
			
			echo "<xml><return_code><![CDATA[FAIL]]></return_code></xml>";
			
		}
		die;
	}

	//微信支付 package
	function getBizPackage($data){
		
		$wx=wechat();
		// 订单总额
        $totalFee = ($data['pay_total'])*100;
        // 随机字符串
        $nonceStr = $wx->generateNonceStr();	

		$config=payment_config('weixin');

        // 时间戳
        $timeStamp = strval(time());		
		
		$pack = array(
	        'appid' =>$config['appid'],
	        'body' => $data['subject'],
	        'mch_id' =>$config['weixin_partner'],
	        'nonce_str' => $nonceStr,
	        'notify_url' =>request()->domain().url('payment/jsskd_notify'),
	        'spbill_create_ip' => get_client_ip(),
	        'openid' => $wx->getOpenId(),
	        // 外部订单号
	        'out_trade_no' => $data['pay_order_no'],
	        'timeStamp' => $timeStamp,
	        'total_fee' => $totalFee,
	        'trade_type' => 'JSAPI'
		);
	
        $pack['sign'] = $wx->paySign($pack);

        $xml = $wx->toXML($pack);

        $ret = Curl::post('https://api.mch.weixin.qq.com/pay/unifiedorder', $xml);					

        $postObj = json_decode(json_encode(simplexml_load_string($ret, 'SimpleXMLElement', LIBXML_NOCDATA)));
	
		if (empty($postObj->prepay_id) || $postObj->return_code == "FAIL") {
		   
              return json(['ret_code'=>11,'bizPackage'=>'']);			  
        } else {
 
            $packJs = array(
                'appId' => $config['appid'],
                'timeStamp' => $timeStamp,
                'nonceStr' => $nonceStr,
                'package' => "prepay_id=" . $postObj->prepay_id,
                'signType' => 'MD5'
            );
		
            $JsSign = $wx->paySign($packJs);			               
		
            $p['timestamp'] = $timeStamp;
			$p['nonceStr'] = $nonceStr;							
			$p['package'] = "prepay_id=" . $postObj->prepay_id;
			$p['signType'] = 'MD5';
            $p['paySign'] = $JsSign;
			
			return json(['ret_code'=>0,'bizPackage'=>$p]);
         
	  }			

	}
}
?>