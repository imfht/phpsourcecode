<?php

namespace app\muushop\controller;

use think\Controller;
use app\muushop\controller\Base;

class Order extends Base {

	protected $product_cats_model;
	protected $product_model;
	protected $order_model;
	protected $order_logic;
	protected $cart_model;
	protected $pay_model;
	protected $user_address_model;
	protected $user_coupon_model;

	protected $list_num;


	function _initialize()
	{
		parent::_initialize();
		$this->product_cats_model    = model('muushop/MuushopProductCats');
		$this->product_model         = model('muushop/MuushopProduct');
		$this->order_model           = model('muushop/MuushopOrder');
		$this->order_logic           = model('muushop/MuushopOrder', 'logic');
		$this->cart_model            = model('muushop/MuushopCart');
		$this->pay_model             = model('muushop/MuushopPay');
		$this->user_address_model    = model('muushop/MuushopUserAddress');
		$this->user_coupon_model     = model('muushop/MuushopUserCoupon');
	}
	/**
	 * 用户下单
	 * @return [type] [description]
	 */
	public function makeOrder()
	{
		$this->init_user();
		//购物车购买参数
		$cart_id = input('cart_id','','text');
		//初始化总价格为0
		$real_price = 0;
		//购物车购买
		if($cart_id){
			$products = $this->cart_model->getDataByIds($cart_id,get_uid());

			if(empty($products)){
				$this->error('参数错误');
			}
			//初始化运费模板ID为0
			$tmp_price = 0;
			//初始化总价
			$real_price = 0;
			//初始化总数量
			$real_quantity = 0;
			foreach($products as &$val){
	            $val['product']['price'] = sprintf("%01.2f", $val['product']['price']/100);//将金额单位分转成元
	            $val['product']['ori_price'] = sprintf("%01.2f", $val['product']['ori_price']/100);
	            $val['total_price'] = $val['product']['price']*$val['quantity'];
	            $val['total_price'] = sprintf("%01.2f", $val['total_price']);
	            //购物车商品总价格
	            $real_price += $val['total_price'];
	            //购物车商品总数量
	            $real_quantity += $val['quantity'];
	            //运费模板id选择价格最贵的商品
	            if($val['product']['price'] >= $tmp_price){
	            	$tmp_price = $val['product']['price'];
	            	$delivery_id = $val['product']['delivery_id'];
	            }
	        }
	        unset($val);
	        unset($tmp_price);
	        $real_price = sprintf("%01.2f", $real_price);

	        $this->assign('delivery_id', $delivery_id);
			$this->assign('real_price',$real_price);
			$this->assign('real_quantity', $real_quantity);
	        $this->assign('cart_id',$cart_id);
	        $this->assign('products', $products);
		}
		
	    //获取可用优惠卷列表
	    $map['uid'] = get_uid(); 
	    $map['min_price'] = [['<=',$real_price*100],['=',0],'or'];
	    $map['expire_time'] = ['>',time()];
	    $map['order_id'] = 0;

	    $enable_coupon = $this->user_coupon_model->getList($map);
	    
	    foreach($enable_coupon as $key=>$val){
	    	$info = $val['info'];
	    	$val['min_price'] = sprintf("%01.2f", $val['min_price']/100);
	        //将金额单位分转成元
	        $val['discount'] = sprintf("%01.2f", $val['discount']/100);
	        
            $enable_coupon[$key]['info'] = $info;
	    }
	    unset($val);
	    unset($map);
	    $this->assign('enable_coupon',$enable_coupon);

	    //允许抵用消费的积分类型
        $able_score = modC('MUUSHOP_SCORE_TYPE','','muushop');
        if(!empty($able_score)) {
        	$able_score_id = substr($able_score,5);
            $map['id'] = $able_score_id;
            $map['status'] = 1;
            $score = model('ucenter/Score')->getType($map);
            $score['quantity'] = model('ucenter/Score')->getUserScore(get_uid(), $score['id']);//用户积分数量
            $score['prop'] = modC('MUUSHOP_SCORE_PROP','','muushop');
            $this->assign('score',$score);//允许抵用的积分类型
        }

	    //获取允许的支付方式
	    $paytype_conf = modC('MUUSHOP_PAYMENT','','Muushop');
	    $paytype = model('MuushopPay')->getPaytype($paytype_conf);
	    $this->assign('paytype', $paytype);

	    //余额支付（依赖钱包模块）该版本暂不提供

	    //返回页面
		return $this->fetch();
	}

	/**
	 * 订单下单完成后成功页
	 * @return [type] [description]
	 */
	public function finish()
	{
		$order_no = input('order_no');
		$order = $this->order_model->getDataByOrderNO($order_no);

		if(empty($order)){
			$this->error('参数错误');
		}
		//赋值
		$this->assign('data',$order);
		//返回页面
		return $this->fetch();
	}
	
	/**
	 * 支付订单
	 * @return [type] [description]
	 */
	public function pay()
	{
		if (request()->isPost()){
			
			$order_id = input('id', 0, 'intval');
			$channel = input('channel','','text');

			$order = $this->order_model->getDataById($order_id);
			
			if (!$order || !($order['uid'] == get_uid())){
				$this->error('参数错误');
			}
			$ret = $this->order_logic->pay($order,$channel);
			if ($ret){
				
				if($order['pay_type'] == 'onlinepay' && !empty($channel)) {
					if(!(strpos($channel,'alipay') === false)){
						//调用支付宝支付插件
						
						hook('alipay',[
							'app' => 'muushop',//应用
							'model' => 'MuushopOrder',
							'order_no' => $order['order_no'],//订单号
							'amount' => $order['paid_fee'],
							'return_url' => get_http_https().$_SERVER['SERVER_NAME'].url('finish',['order_no'=>$order['order_no']]),//同步返回地址
							'passback_params' => 'muushop/MuushopOrder', //处理支付系统的异步通知模型，格式约定 应用/模型 notify处理方法
						]);
					}

					if(!(strpos($channel,'wx') === false)){
						//调用微信支付插件
						hook('wxpay',[
							'app' => 'muushop',
							'model' => 'MuushopOrder',
							'order_no' => $order['order_no'],
							'amount' => $order['paid_fee'],
							'return_url' => get_http_https().$_SERVER['SERVER_NAME'].url('finish'),
							'attach' => 'muushop/MuushopOrder'
						]);
					}
				}
			}else{
				$this->error('数据处理错误');
			}
		}else{
			$order_no = input('order_no');
			$order = $this->order_model->getDataByOrderNO($order_no);
			if($order){
				$channel = $this->pay_model->channel();
				$this->assign('channel',$channel);
				$this->assign('order',$order);
				return $this->fetch();
			}else{
				$this->error('参数错误');
			}
		}
	}

}