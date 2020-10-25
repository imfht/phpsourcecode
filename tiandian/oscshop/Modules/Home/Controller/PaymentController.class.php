<?php
/**
 * oscshop 电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
namespace Home\Controller;

class PaymentController extends CommonController {
	//会员中心页面，去付款
	function confirm_pay(){
		if(I('token')!=md5(session('pay_token'))){
			$url=U('/checkout');
			@header("Location: ".$url);
			die();
		}
		$order=M('order')->where(array('order_id'=>get_url_id('id')))->find();
		
		$data['notify_url']=C('SITE_URL').U('Payment/alipay_notify');
		$data['return_url']=C('SITE_URL').U('Payment/alipay_return');
		$data['order_type']='goods_buy';
		$data['subject']='购买商品';
		$data['name']=$order['shipping_name'];
		$data['pay_order_no']=$order['order_num_alias'];
		$data['pay_total']=(float)$order['total'];		
		
		storage_user_action(session('user_auth.uid'),session('user_auth.username'),C('FRONTEND_USER'),'点击了去支付订单 '.$order['order_num_alias']);
		
		$url=$this->pay_api($order['payment_code'], $data);			
		
		@header("Location: ".$url);
	
		die();		
	}
	
	
	
	/**
	 * $pay_type 购买商品，还是预存款
	 * $order 订单信息
	 */
	function pay_api($payment_method,$order){
		
		if($payment_method=='alipay'){			
			
			$alipay= new \Lib\Payment\Alipay(get_payment_config('alipay'),$order);
			return $alipay->get_payurl();
		}
	}
	//写入订单
	function pay(){
		$json=array();
		if(I('token')!=md5(session('token'))){
			$url=U('/checkout');
			@header("Location: ".$url);
			die();
		}		

		$cart=new \Lib\Cart();	
		
		// 验证商品数量		
		$goodss = $cart->get_all_goods();
		//付款人
		$payment=M('Member')->find(session('user_auth.uid'));
		//收货人 
		$shipping=M('Address')->find(session('shipping_address_id'));
		
		$data['member_id']=session('user_auth.uid');
		$data['name']=session('user_auth.username');
		$data['email']=$payment['email'];
		$data['telephone']=$payment['telephone'];
		
		$data['shipping_name']=$shipping['name'];
		$data['shipping_address']=$shipping['address'];
		$data['shipping_tel']=$shipping['telephone'];
		
		$data['shipping_province_id']=$shipping['province_id'];
		$data['shipping_city_id']=$shipping['city_id'];
		$data['shipping_country_id']=$shipping['country_id'];		
		
		$data['shipping_method']=session('shipping_method');
		$data['payment_method']=session('payment_method');
		$data['address_id']=session('shipping_address_id');

		$data['user_agent']=$_SERVER['HTTP_USER_AGENT'];
		$data['date_added']=time();
		$data['comment']=session('comment');
				
		$subject='';
		if($goodss){				
				
				$sm=D('Transport')->calc_transport(session('shipping_method'),
				session('weight'),
				session('shipping_city_id')
				);	
				$t=0;		
				foreach ($goodss as $goods) {
					
					$option_data = array();
	
					foreach ($goods['option'] as $option) {
						
						$value = $option['value'];						
	
						$option_data[] = array(
							'goods_option_id'       => $option['goods_option_id'],
							'goods_option_value_id' => $option['goods_option_value_id'],
							'option_id'               => $option['option_id'],
							'option_value_id'         => $option['option_value_id'],								   
							'name'                    => $option['name'],
							'value'                   => $value,
							'type'                    => $option['type']
						);					
					}
					
					$t+=$goods['total'];					
	
					$goods_data[] = array(
						'goods_id'   => $goods['goods_id'],
						'name'       => $goods['name'],
						'model'      => $goods['model'],		
						'option'     => $option_data,						
						'quantity'   => $goods['quantity'],
						'subtract'   => $goods['subtract'],
						'price'      => $goods['price'],
						'total'      => $goods['total']				
					); 		
			
					$subject.=$goods['name'].' ';					
						
					}
					$data['total']=($t+$sm['price']);
					$data['goodss'] = $goods_data;
					$data['order_num_alias']=build_order_no();
					
					$data['totals'][0]=array(
						'code'=>'sub_total',
						'title'=>'商品价格',
						'text'=>'￥'.$t,
						'value'=>$t				
					);
					$data['totals'][1]=array(
						'code'=>'shipping',
						'title'=>'运费',
						'text'=>'￥'.$sm['price'],
						'value'=>$sm['price']				
					);				
					$data['totals'][2]=array(
						'code'=>'total',
						'title'=>'总价',
						'text'=>'￥'.($t+$sm['price']),
						'value'=>($t+$sm['price'])				
					);
				
				$oid=D('Order')->addOrder($data);
				
				if($oid){				
					session('cart_total',null);
					$order['notify_url']=C('SITE_URL').U('Payment/alipay_notify');
					$order['return_url']=C('SITE_URL').U('Payment/alipay_return');
					$order['order_type']='goods_buy';
					$order['subject']=$subject;
					$order['name']=session('shipping_name');
					$order['pay_order_no']=$data['order_num_alias'];
					$order['pay_total']=($t+$sm['price']);			
					
					session('cart',null);
					session('total',null);					
					session('shipping_address_id',null);	
					
					$url=$this->pay_api(session('payment_method'), $order);
					@header("Location: ".$url);
				
					die();
				}else{
					$url=U('/checkout');
					@header("Location: ".$url);
					
					die();
				}
				
			}
			
	}

	function de_bug($content){
		$file = ROOT_PATH."/Tmp/wxpay_debug.php";
		file_put_contents($file,$content);	
	}

	//数据以post方式返回
	function alipay_notify(){
		
		$alipay= new \Lib\Payment\Alipay(get_payment_config('alipay'));
		
		$verify_result = $alipay->verifyNotify();
		
		if($verify_result) {
			
			//$this->de_bug('success');
			
			//商户订单号	
			//$out_trade_no = $_POST['out_trade_no'];	
			//支付宝交易号	
			//$trade_no = $_POST['trade_no'];	
			//交易状态
			//$trade_status = $_POST['trade_status'];
			
			if($_POST['trade_status'] == 'TRADE_FINISHED') {
				//$this->de_bug('TRADE_FINISHED');
				
		    }
		    else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
				//$this->de_bug('TRADE_SUCCESS');
				
				$order=M('Order')->getByOrderNumAlias($_POST['out_trade_no']);
				
				if($order&&($order['order_status_id']!=C('paid_order_status_id'))){
						//支付完成						
						$o['order_id']=$order['order_id'];
						$o['order_status_id']=C('paid_order_status_id');
						$o['date_modified']=time();
						$o['pay_time']=time();
						M('Order')->save($o);
						
						$oh['order_id']=$order['order_id'];
						$oh['order_status_id']=C('paid_order_status_id');
				
						$oh['comment']='买家已付款';
						$oh['date_added']=time();
						$oh['notify']=1;
						M('OrderHistory')->add($oh);
						
						$model=new \Admin\Model\OrderModel();	   
					    $this->order=$model->order_info($order['order_id']);
					    $html=$this->fetch('Mail:order');				   
					    think_send_mail($order['email'],$order['name'],'下单成功-'.C('SITE_NAME'),$html);
						
						storage_user_action(session('user_auth.uid'),session('user_auth.username'),C('FRONTEND_USER'),'支付了订单 '.$order['order_num_alias']);
						
						//@header("Location: ".U('/pay_success'));	
											
				}
				
		        
				echo "success";		
		    }
		
			
			
		}else{
			//$this->de_bug('fail');
			echo "fail";
		}
		
		
		
	}

	function alipay_return(){
		
		$alipay= new \Lib\Payment\Alipay(get_payment_config('alipay'));
		
		//对进入的参数进行远程数据判断
		$verify = $alipay->return_verify();
	
		if($verify){
			$order=M('Order')->getByOrderNumAlias($_GET['out_trade_no']);
			
			if($order['order_status_id']==C('paid_order_status_id')){
				@header("Location: ".U('/pay_success'));	
				die;
			}
			
			if($order&&($order['order_status_id']!=C('paid_order_status_id'))){
				//支付完成
				if($_GET['trade_status']=='TRADE_SUCCESS'){					
					
					$o['order_id']=$order['order_id'];
					$o['order_status_id']=C('paid_order_status_id');
					$o['date_modified']=time();
					$o['pay_time']=time();
					M('Order')->save($o);
					
					$oh['order_id']=$order['order_id'];
					$oh['order_status_id']=C('paid_order_status_id');
			
					$oh['comment']='买家已付款';
					$oh['date_added']=time();
					$oh['notify']=1;
					M('OrderHistory')->add($oh);
					
					$model=new \Admin\Model\OrderModel();	   
				    $this->order=$model->order_info($order['order_id']);
				    $html=$this->fetch('Mail:order');				   
				    think_send_mail($order['email'],$order['name'],'下单成功-'.C('SITE_NAME'),$html);
					
					storage_user_action(session('user_auth.uid'),session('user_auth.username'),C('FRONTEND_USER'),'支付了订单 '.$order['order_num_alias']);
					
					@header("Location: ".U('/pay_success'));	
				}						
			}else{
				die('订单不存在');
			}
			
		}else{
			die('支付失败');
		}		
		
	}
}