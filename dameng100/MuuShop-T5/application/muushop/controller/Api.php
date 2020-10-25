<?php
namespace app\muushop\controller;

use think\Controller;
use app\muushop\model\MuushopService;
use think\Db;

class Api extends Controller {
	protected $product_model;
	protected $cart_model;
	protected $order_model;
	protected $order_logic;
	protected $user_address_model;
	protected $delivery_model;
	protected $coupon_model;
	protected $user_coupon;
	protected $coupon_logic;
	protected $product_comment_model;
	protected $service_model;

	function _initialize()
	{   
		$this->product_model         = model('muushop/MuushopProduct');
		$this->cart_model            = model('muushop/MuushopCart');
		$this->order_model           = model('muushop/MuushopOrder');
		$this->order_logic           = model('muushop/MuushopOrder', 'logic');
		$this->user_address_model    = model('muushop/MuushopUserAddress');
		$this->delivery_model        = model('muushop/MuushopDelivery');
		$this->coupon_model          = model('muushop/MuushopCoupon');
		$this->user_coupon           = model('muushop/MuushopUserCoupon');
		$this->coupon_logic       	 = model('muushop/MuushopCoupon', 'logic');
		$this->product_comment_model = model('muushop/MuushopProductComment');
		$this->service_model         = model('muushop/MuushopService');
	}

	protected function init_user(){
		if(_need_login() === false){
			$this->error('需要登录');
		}
	}

//*****************购物车接口******************//
	public function cart($action = '')
	{
		$this->init_user();
		switch($action)
		{
			case 'count'://获取用户购物车内数量
				$totalCount = $this->cart_model->getCountByUid(get_uid());
				if($totalCount){
					$this->success('执行成功。',null,$totalCount);
				}else{
					$this->error();
				}
			break;

			case 'add': //加入购物车
				if(request()->isPost()){
					$data = input('post.');
					$data['uid'] = get_uid();
					$res = $this->cart_model->editData($data);
					if ($res){
						$this->success('加入购物车成功','',$res);
					}else{
						$this->error('加入购物车时发生错误');
					}
				}
			break;

			case 'edit': //编辑购物车
				if(request()->isPost()){
					$data = input('post.');
					$data['uid'] = get_uid();

					$res = $this->cart_model->editData($data);
					if ($res){
						$this->success('成功修改购物车数据');
					}else{
						$this->error('修改数据时发生错误');
					}
				}
			break;

			case 'delete': //删除购物车商品
				$ids = input('ids/a');
				$res = $this->cart_model->deleteData($ids, get_uid());
				if ($res){
					$this->success('商品删除成功',url('muushop/cart/index'));
				}else{
					$this->error('商品删除时发生错误');
				}
			break;

			case 'list': //购物车商品列表

			break;
		}
	}

//*****************购物车接口 end******************//
	/**
	 * 计算运费json接口
	 * @param int $id 运费模板ID
	 * @param int $areaid 地区ID代码 依赖ChinaCity插件
	 * @param int $quantity 购买的商品总是
	 * @param int express 运输方式 如：express\ems\self
	 * @return [json] [根据模板ID返回模板详细JSON字符串]
	 */
	public function delivery(){
		$id = input('id',0,'intval');
		$address_id = input('address_id',0,'intval');
		$quantity = input('quantity',0,'intval');
		$express = input('express','','text');

		if($id==0 || empty($id)){//id为空或为0
			$data['delivery_fee']=0;
		}else{
			//$address = $this->user_address_model->getDataById($areaid);
			if($express){
				$address['delivery'] = $express;
			}
			$delivery_fee = $this->order_logic->calcDeliveryFee($id,$address_id, $quantity);
		}
		
		//JSON返回数据
		if(isset($delivery_fee)){
			$this->success('获取成功',url('muushop/cart/index'),$delivery_fee);
		}else{
			$this->error();
		}
	}

//***********************地址类接口*****************************//
//
	public function address($action = 'list')
	{
		switch($action)
		{
			case 'list'://获取用户收货地址列表
				$this->init_user();
				$map['uid'] = is_login();
				$list = $this->user_address_model->getList($map);
				$first = 0;
				foreach($list as &$val){
		            $val['province'] = Db::name('district')->where(['id' => $val['province']])->value('name');
		            $val['city'] = Db::name('district')->where(['id' => $val['city']])->value('name');
		            $val['district'] = Db::name('district')->where(['id' => $val['district']])->value('name');

		            if($val['update_time']>$first){
		            	$first=$val['update_time'];
		            	$val['first']=1;
		            }else{
		            	unset($val['first']);
		            }
				}
				unset($val);

				//组装JSON返回数据
				if($list){
					$this->success('操作成功。',null,$list);
				}else{
					$this->error('操作失败。');
				}
			break;

			case 'edit': //编辑、新增收货地址
				$this->init_user();
				if (request()->isPost()){
					$data['id'] 		= input('post.id',0,'intval');
					$data['name'] 		= input('post.name','','text');
					$data['phone'] 		= input('post.phone',0,'intval');
					$data['province'] 	= input('post.province',0,'intval');
					$data['city'] 		= input('post.city',0,'intval');
					$data['district'] 	= input('post.district',0,'intval');
					$data['address'] 	= input('post.address','','text');
					$data['uid'] 	= get_uid();
					//dump($data);exit;
					//每个用户只允许创建20个收货地址
					$map['uid'] = get_uid();
					$list = $this->user_address_model->getList($map);
					
					if(count($list)<=8){
						$ret = $this->user_address_model->editData($data);
					}else{
						$this->error('最多只能添加20条收货地址。');
					}

					if ($ret){
						$this->success('操作成功。');
					}else{
						$this->error('操作失败。');
					}	
				}
			break;

			case 'delete': //删除收货地址
				$this->init_user();
				if (request()->isPost()){
					$ids = input('post.id',0,'intval');
					$uid = get_uid();
					$res = $this->user_address_model->deleteData($ids);
					if ($res){
						$this->success('操作成功。', url('user/address'));
					}else{
						$this->error('操作失败。');
					}
				}else{
					$this->error('提交方式错误或未登陆。');
				}
			break;

			case 'first': //设置为默认地址
				$id = input('id','','intval');
				$data['id'] = $id;
				$data['update_time'] = time();

				$ret = $this->user_address_model->editData($data);
				if ($ret){
					$this->success('操作成功。', url('user/address'));
				}else{
					$this->error('操作失败。');
				}
			break;
		}
	}

//***********************优惠卷接口*****************************//
	public function coupon($action = 'list')
	{
		switch($action)
		{
			case 'list'://所有可领取优惠卷列表
				$map['expire_time'] = [['>',time()],['=',0],'or'];
				$res = $this->coupon_model->getListByPage($map,'id desc','*',20);
				if ($res){
					$this->success('请求成功','',$res);
				}else{
					$this->error('请求失败');
				}
			break;

			case 'mylist': //我的可用优惠卷
				if (request()->isPost()){
					$paid_fee = input('paid_fee');//当前消费使用金额
					if($price){
						$map['min_price'] = ['<=',$paid_fee];
					}
					
					$map['uid'] = get_uid();
					$map['expire_time'] = [['>',time()],['=',0],'or'];
					$map['order_id'] = 0;
					$res = $this->user_coupon->getListByPage($map,'id desc','*',20);
					if ($res){
						$this->success('请求成功','',$res);
					}else{
						$this->error('请求失败');
					}
				}

			break;

			case 'get': //根据ID获取优惠卷信息

				$id = input('id',0,'intval');
				$ret = $this->user_coupon->getDataById($id);
				$ret['info']['rule']['discount'] = sprintf("%01.2f", $ret['info']['rule']['discount']/100);//将金额单位分转成元
				//组装JSON返回数据
				if($ret){
					$result['code']=1;
					$result['msg'] = 'success';
					$result['data'] = $ret;
				}else{
					$result['code']=0;
					$result['msg'] = 'error';
				}
				return $result;
			break;

			case 'receive': //领取一张优惠卷
				$this->init_user();
				$id = input('id');
				if (empty($id) || !($coupon = $this->coupon_model->getDataById($id))){
					$this->error('优惠券不存在');//id 解密对不上
				}

				$res = $this->coupon_logic->addCouponToUser($coupon['id'], get_uid());

				if ($res){
					$this->success('领取成功');
				}else{
					$this->error('领取失败，' . $this->coupon_logic->error_str);
				}
			break;
		}
	}

//***********************订单类接口*****************************//
//
	public function order($action = 'list')
	{
		$this->init_user();

		switch($action)
		{
			case 'detail'://订单详细

			break;

			case 'markorder'://下单
				
				if (request()->isPost()){
					$inputData = input('post.');

					$order['uid'] = get_uid();
					//购物车购买
					$order['cart_id'] = $inputData['cart_id'];
					//收货地址
					$order['address_id'] = $inputData['address_id'];
					//组装要提交的数据
					$order['delivery_id'] = $inputData['delivery_id'];
					//支付方式获取
					$order['pay_type'] = $inputData['pay_type'];
					//留言 发票 提货时间 等其他信息
					$order['info'] = json_encode($inputData['info']);
					//在线支付的支付类型
					if($inputData['pay_type'] == 'onlinepay') {
						if(isset($inputData['channel'])){
							$order['channel'] = $inputData['channel'];
						}else{
							$order['channel'] = '';
						}
						
					}else{
						$order['channel'] = '';
					}
					//使用优惠劵
					if(isset($inputData['coupon_id'])) $order['coupon_id'] = $inputData['coupon_id'];
					//使用的积分抵用数据
					if(isset($inputData['use_point'])) $order['use_point'] = $inputData['use_point'];
					
					//增加下单后的钩子
					$res = $this->order_logic->make_order($order);
					if ($res){
						//获取订单数据
						$order = $this->order_model->getDataById($res);
						//货到付款跳转链接
						$url = '';
						if($order['pay_type'] == 'delivery'){
							$url = url('muushop/Order/finish',['order_no'=>$order['order_no']]);
						}
						if($order['pay_type'] == 'onlinepay'){
							$url = url('muushop/Order/pay',['order_no'=>$order['order_no']]);
						}
		                $this->success('下单成功，页面即将跳转！',$url,$order);
					}else{
						$this->error('下单失败.' . $this->order_logic->error_str);
					}
				}
			break;

			case 'pay'://支付渠道提交

				if (request()->isPost()){
					$order_id = input('id', 0, 'intval');
					$channel = input('channel','','text');

					$order = $this->order_model->getDataById($order_id);
					$order['channel'] = $channel;

					if (!$order || !($order['uid'] == get_uid())){
						$result['code'] = 0;
						$result['msg'] = '参数错误';
					}
					$ret = $this->order_logic->pay($order);
					if ($ret){
						$result['code']=1;
						$result['msg'] = '确认成功';
						$result['data'] = $ret;
						$result['url'] = url('muushop/User/orders');
					}else{
						$result['code']=0;
						$result['msg'] = '确认失败';
					}
				}

			break;

			case 'cannel'://取消订单
				
				if (request()->isPost()){
					if (!($order_id = input('id', 0, 'intval'))
						|| !($order = $this->order_model->getDataById($order_id))
						|| !($order['uid'] == is_login())
					){
						$result['code']=0;
						$result['msg'] = '参数错误';
					}
					$ret = $this->order_logic->cancalOrder($order);
					if ($ret){
						$result['code']=1;
						$result['msg'] = '订单取消成功';
						$result['url'] = url('muushop/User/orders');
					}else{
						$result['code']=0;
						$result['msg'] = '取消失败';
					}
					return $result;
				}
				
			break;

			case 'confirm'://确认收货
				
				if (request()->isPost()){
					$order_id = input('id', 0, 'intval');
					$order = $this->order_model->getDataById($order_id);

					if (!$order || !($order['uid'] == get_uid())){
						$result['code'] = 0;
						$result['msg'] = '参数错误';
					}
					$ret = $this->order_logic->recvGoods($order);
					if ($ret){
						$result['code']=1;
						$result['msg'] = '确认成功';
						$result['data'] = $ret;
						$result['url'] = url('muushop/User/orders');
					}else{
						$result['code']=0;
						$result['msg'] = '确认失败';
					}

				}else{
					$result['code']=0;
					$result['msg'] = '提交方式错误或未登陆';
				}
				return $result;
			
			break;

			case 'count'://根据状态值获取订单数量

				$option['uid'] = get_uid();
				$option['status'] = input('status','0','text');
				if(is_numeric($option['status'])){
					$option['status'] = $option['status'];
				}else{
					$tempArr = explode(',',$option['status']);
					$option['status']= [];
					foreach($tempArr as $v){
						$option['status'][] = ['eq',$v];
					}
					$option['status'][] = 'or';
				}
				$order_count = $this->order_model->getCount($option);

				if ($order_count){
					$this->success('操作成功。','',$order_count);
				}else{
					$this->error('还没有该类型订单。');
				}

			break;

			case 'list'://订单列表
				
				$option['uid'] = get_uid();
				$option['status'] = input('status','0','text');//获取订单状态的数字
				if(is_numeric($option['status'])){
					$option['status'] = $option['status'];
				}else{
					$tempArr = explode(',',$option['status']);
					$option['status']= [];
					foreach($tempArr as $v){
						$option['status'][] = ['eq',$v];
					}
					$option['status'][] = 'or';
				}

				$order_list = $this->order_model->getListByPage($option,$order='create_time desc',$field='*',$r=20);
				$order_list = empty($order_list)?array(): $order_list;

				foreach($order_list as &$val){
					$val['paid_fee'] = sprintf("%01.2f", $val['paid_fee']/100);//将金额单位分转成元
					foreach($val['products'] as &$products){
						$products['temporary'] = explode(';',$products['sku_id']);
						
						if(empty($products['temporary'][1])){
							unset($products['temporary'][1]);
						}

						$products['id'] = $products['temporary'][0];
						unset($products['temporary'][0]);//删除临时sku_id数组的ID
						$products['temporary'] = array_values($products['temporary']);
						
						if(!empty($products['temporary'])){//数组不为空时写sku
							$products['sku'] =(empty($products['temporary'])?'':$products['temporary']);
						}
						unset($products['temporary']);//删除临时sku_id数组
					}
					unset($products);
				};
				unset($val);
				if($order_list){
					$result['code']=1;
					$result['msg'] = 'success';
					$result['data'] = $order_list;
				}else{
					$result['code']=0;
					$result['msg'] = 'error';
				}
				return $result;
			break;
		}
	}
	/**
	 * 售后服务接口
	 * @return [type] [description]
	 */
	public function service($action = '')
	{
		$this->init_user();
		switch($action)
		{
			case 'apply'://申通售后
				if (request()->isPost()){

					$inputData = input('post.');
					$inputData['status'] = MuushopService::SERVICE_UNDER_NEGOTATION;
					//判断是否已提交申请
					$have = $this->service_model->getDataByOrderIdAndProductId($inputData['order_id'],$inputData['product_id']);
					if($have){
						$this->error('已提交过申请。');
					}
					$res = $this->service_model->editData($inputData);
					if ($res){
						$this->success('操作成功。','',$res);
					}else{
						$this->error('操作失败。');
					}
				}
			break;

			case 'return_express': //买家提交返回货物物流信息,更改状态
				if (request()->isPost()){

					$data['id'] = input('post.id');
					$data['status'] = MuushopService::SERVICE_GOOD_RETURN_OK;
					$ShipperValue = input('post.ShipperValue');
					
					$ShipperValue = explode(',',$ShipperValue);
					$d['return_express']['ShipperName'] = $ShipperValue[0];
					$d['return_express']['ShipperCode'] = $ShipperValue[1];
					$d['return_express']['LogisticCode'] = input('post.LogisticCode');
					$data['info'] = json_encode($d);

					
					$res = $this->service_model->editData($data);
					if ($res){
						$this->success('操作成功。','',$res);
					}else{
						$this->error('操作失败。');
					}
				}
			break;

			case 'confirm': //买家确认收到退货，更改状态
				if (request()->isPost()){
					$data['id'] = input('post.id');
					$data['status'] = MuushopService::SERVICE_END;

					$res = $this->service_model->editData($data);
					if ($res){
						$this->success('操作成功。','',$res);
					}else{
						$this->error('操作失败。');
					}
				}
			break;
		}
	}

}



