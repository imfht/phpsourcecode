<?php
namespace app\muushop\controller;

use think\Controller;
use think\Db;

class User extends Base {

	protected $product_model;
	protected $cart_model;
	protected $order_model;
	protected $order_logic;
	protected $user_address;
	protected $user_coupon;
	protected $service_model;

function _initialize()
	{
		parent::_initialize();
		$this->init_user();
		$this->product_model      		= model('muushop/MuushopProduct');
		$this->cart_model         		= model('muushop/MuushopCart');
		$this->order_model        		= model('muushop/MuushopOrder');
		$this->order_logic        		= model('muushop/MuushopOrder', 'logic');
		$this->user_address       		= model('muushop/MuushopUserAddress');
		$this->user_coupon        		= model('muushop/MuushopUserCoupon');
		$this->service_model    		= model('muushop/MuushopService');

	}
	/**
	 * 商城用户中心
	*/
	public function index()
	{
		return $this->fetch();
	}

	/**
	 * 我的订单
	 */
	public function orders($action = 'list')
	{
		switch($action)
		{
			case 'delivery_info':

				$id = input('id',0,'intval');
				empty($id) && $this->error('订单参数错误',1);
				$order = $this->order_model->getDataById($id);
				
				$this->assign('order',$order);
				$this->assign('delivery',delivery_addons());
				
				return $this->fetch('muushop@public/_delivery');

			break;

			case 'detail':
				$id = input('id',0,'intval');
				
				$order = $this->order_model->getDataById($id);
				$order = $order->toArray();
				
				$order['user_info'] = query_user('nickname',$order['uid']);
				$order['address']["province"] = Db::name('district')->where(['id' => $order['address']["province"]])->value('name');
			    $order['address']["city"] = Db::name('district')->where(['id' => $order['address']["city"]])->value('name');
			    $order['address']["district"] = Db::name('district')->where(['id' => $order['address']["district"]])->value('name');

				    //设置支付类型
				    switch ($order['pay_type']){
				    	case 'balance':
				    		$order['pay_type_cn']="余额支付";
				    	break;
				    	case 'delivery':
				    		$order['pay_type_cn']="货到付款";
				    	break;
				    	case 'onlinepay':
				    		$order['pay_type_cn']="在线支付";
				    	break;
				    	default:
				    		$order['pay_type_cn']="未设置";
				    }
				    
					$order['paid_fee']='¥ '.sprintf("%01.2f", $order['paid_fee']/100);
					$order['delivery_fee']='¥ '.sprintf("%01.2f", $order['delivery_fee']/100);
					$order['discount_fee']='- ¥ '.sprintf("%01.2f", $order['discount_fee']/100);

					if(!empty($order['products'])){
						foreach($order['products'] as &$val){
							//商品列表价格单位转为元
							$val['paid_price']='¥ '.sprintf("%01.2f", $val['paid_price']/100);
							//sku_id转为数组
							$val['sku'] = explode(';',$val['sku_id']);
							unset($val['sku'][0]);
						}
					}
					unset($val);
					//dump($order);exit;
					$this->assign('order',$order);
					$this->assign('delivery',delivery_addons());
					return $this->fetch('user/order_detail');
			break;
			default:
				
				$r = 20;
				$map['uid'] = is_login();
				$map['status'] = input('status','0','text');//获取订单状态的数字

				if($map['status'] == 0){
					unset($map['status']);
				}else{

					if(is_numeric($map['status'])){
						$map['status'] = $map['status'];
					}else{
						$tempArr = explode(',',$map['status']);
						$map['status']= [];
						foreach($tempArr as $v){
							$map['status'][] = ['eq',$v];
						}
						$map['status'][] = 'or';
					}
				}
				

				$order_list = $this->order_model->getListByPage($map,'create_time desc','*',$r);

				$page = $order_list->render();
				$order_list = $order_list->toArray()['data'];
				
				foreach($order_list as &$val){

					$val['paid_fee'] = sprintf("%01.2f", $val['paid_fee']/100);//将金额单位分转成元

					foreach($val['products'] as &$products){
						$products['temporary'] = explode(';',$products['sku_id']);
						//产品ID
						$products['id'] = $products['temporary'][0];
						//删除临时sku_id数组的ID
						unset($products['temporary'][0]);
						$products['temporary'] = array_values($products['temporary']);
						
						if(!empty($products['temporary'])){//数组不为空时写sku
							$products['sku'] =(empty($products['temporary'])?'':$products['temporary']);
						}
						unset($products['temporary']);//删除临时sku_id数组
						//售后服务信息
						$products['service'] = $this->service_model->getDataByOrderIdAndProductId($val['id'], $products['id']);
						if(!empty($products['service'])) {
							$products['service'] = $products['service']->toArray();
						}
					}
					unset($products);

					$val['delivery_fee']='¥ '.sprintf("%01.2f", $val['delivery_fee']/100);
					$val['discount_fee']='- ¥ '.sprintf("%01.2f", $val['discount_fee']/100);
					$val['status_str'] = $val['status'];
				};
				unset($val);
				
				$this->assign('order_list',$order_list);
				$this->assign('page',$page);
				
				return $this->fetch('user/orders');
		}
	}

	/**
	 * 用户售后服务
	 * @return [type] [description]
	 */
	public function service($action = ''){

		switch($action)
		{
			case 'apply'://申请售后服务
				
				$product_id = input('product_id',0,'intval');
				$order_id = input('order_id',0,'intval');

				$order = $this->order_model->getDataById($order_id);
				$order = $order->toArray();

				//初始化退换货商品信息
				$product = [];
				foreach($order['products'] as &$val){
					$val['tempArr'] = explode(';',$val['sku_id']);
					if($val['tempArr'][0] == $product_id) {
						$product['product_id'] = $product_id;
						unset($val['tempArr'][0]);
						$product['sku'] = array_values($val['tempArr']);
						$product['order_id'] = $order_id;
						$product['title'] = $val['title'];
						$product['main_img'] = $val['main_img'];
						$product['main_img_src'] = getThumbImageById($val['main_img'],100,100);
						$product["quantity"] = $val['quantity'];
						$product["paid_price"] = $val['paid_price'];
					}
				}
				unset($val);

				$this->assign('action',$action);
				$this->assign('product',$product);
				return $this->fetch('user/service/apply');
			break;

			case 'return_express': //买家提交返回货物物流信息,更改状态
				$id = input('id');//获取点击的id
				$data = $this->service_model->getDataById($id);
				$this->assign('data', $data);

				//初始化物流公司和编码数组
				$this->assign('delivery',delivery_addons());

				return $this->fetch('user/service/express');
			break;

			case 'return_express_info': //退货物流信息

				
				$id = input('id');
				$data = $this->service_model->getDataById($id);
				
				$this->assign('data',$data);
				$this->assign('delivery',delivery_addons());
				return $this->fetch('user/service/return_express_info');

			break;

			case 'replace_express_info': //发货物流信息

				$id = input('id');
				$data = $this->service_model->getDataById($id);
			
				$this->assign('data',$data);
				$this->assign('delivery',delivery_addons());
				return $this->fetch('user/service/replace_express_info');
			break;

			case 'replace_express_info': //发货物流信息

				$id = input('id');
				$data = $this->service_model->getDataById($id);
			
				$this->assign('data',$data);
				$this->assign('delivery',delivery_addons());
				return $this->fetch('user/service/replace_express_info');
			break;

			case 'confirm': //发货物流信息

				$id = input('id');
				$data = $this->service_model->getDataById($id);
			
				$this->assign('data',$data);
				$this->assign('delivery',delivery_addons());
				return $this->fetch('user/service/confirm');
			break;
		}
		
	} 

	/**
	 * 我的优惠卷
	 */
	public function coupon()
	{
		$map['uid'] = get_uid(); 
		$map['expire_time'] = [['>',time()],['=',0],'or'];//已过期优惠卷不显示
	    $map['order_id'] = 0;
	    $coupon = $this->user_coupon->getListByPage($map,'id desc','*',18);
	    $page = $coupon->render();
	    $coupon = $coupon->toArray()['data'];
	    
	    foreach($coupon as &$val){
	    	
	    	$val['min_price'] = sprintf("%01.2f", $val['min_price']/100);
            //将金额单位分转成元
            $val['discount'] = sprintf("%01.2f", $val['discount']/100);
	    }
	    unset($val);
	    $this->assign('page', $page);
	    $this->assign('coupon', $coupon);
		return $this->fetch();
	}

	/**
	 * 我的地址
	 */
	public function address($action='')
	{
		switch($action)
		{
			case 'edit'://编辑添加地址

				$id = input('id',0,'intval');
				$address = $this->user_address->getDataById($id);
				$this->assign('address', $address);
				return $this->fetch('user/address_edit');
				
			break;
			case 'delete'://删除地址

				$id = input('id','','intval');
				$this->assign('id',$id);
				return $this->fetch('user/address_del');

			break;
			default:
				$map['uid'] = get_uid();
				$list = $this->user_address->getList($map,'update_time desc,create_time desc','*');
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
				$this->assign('list', $list);
				return $this->fetch();
		}
	}

	/*
	 * 订单评论
	 * {
	 * product_id:
	 * uid:
	 * order_id:
	 * images:
	 * score:
	 * brief:
	 * sku_id:
	 * 
	 * }
	 */
	public function comment()
	{
		//判断评价嗮图插件是否安装
		$evaluateInstall = '';
		$addon = \think\Hook::get('evaluateSubmit');
		foreach ($addon as $name) {
            if (class_exists($name)) {
            	$class= new $name;
                $evaluateInstall = $class->info['title'];
            }
        }
		$this->assign('evaluateInstall', $evaluateInstall);
		//获取参数
		$id = input('id',0,'intval');
		$order = $this->order_model->getDataById($id);
		$order = $order->toArray();
		
		foreach($order['products'] as &$products){
			$products['temporary'] = explode(';',$products['sku_id']);
			
			$products['product_id'] = $products['temporary'][0];
			unset($products['temporary'][0]);//删除临时sku_id数组的ID
			$products['temporary'] = array_values($products['temporary']);
			
			if(!empty($products['temporary'])){//数组不为空时写sku
				$products['sku'] =(empty($products['temporary'])?'':$products['temporary']);
			}
			unset($products['temporary']);//删除临时sku_id数组
			$products['sku_encode'] = base64_encode($products['sku_id']);
			//$products['sku_decode'] = base64_decode($products['sku_unicode']);
		}
		unset($products);
		$this->assign('order', $order);
		return $this->fetch();
	}
}