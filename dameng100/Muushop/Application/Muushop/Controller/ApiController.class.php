<?php

namespace Muushop\Controller;

use Think\Controller;

class ApiController extends Controller {
	protected $product_model;
	protected $cart_model;
	protected $order_model;
	protected $order_logic;
	protected $user_address_model;
	protected $delivery_model;
	protected $user_coupon;
	protected $coupon_logic;
	protected $product_comment_model;

	function _initialize()
	{   
		$this->product_model      = D('Muushop/MuushopProduct');
		$this->cart_model         = D('Muushop/MuushopCart');
		$this->order_model        = D('Muushop/MuushopOrder');
		$this->order_logic        = D('Muushop/MuushopOrder', 'Logic');
		$this->user_address_model = D('Muushop/MuushopUserAddress');
		$this->delivery_model     = D('Muushop/MuushopDelivery');
		$this->user_coupon        = D('Muushop/MuushopUserCoupon');
		$this->coupon_logic       = D('Muushop/MuushopCoupon', 'Logic');
		$this->product_comment_model = D('Muushop/MuushopProductComment');
	}

	protected function init_user(){
		if(_need_login()){
			return get_uid();
		}else{
			$result['status']=0;
			$result['info'] = '取消失败';
			$this->ajaxReturn($result,'JSON');
		}
	}
	public function result_url(){
		//根据支付方式判断回调地址
		$callback = modC('MUUSHOP_PAY_CALLBACK','','Muushop');
		$result_url=urlencode($callback);//支付成功后跳转回的地址
		//组装JSON返回数据
		if(isset($result_url)){
			$result['status']=1;
			$result['info'] = 'success';
			$result['data'] = $result_url;
		}else{
			$result['status']=0;
			$result['info'] = 'error';
		}
		$this->ajaxReturn($result,'JSON');
	}
	/**
	 * 计算运费json接口
	 * @param int $id 运费模板ID
	 * @param int $areaid 地区ID代码 依赖ChinaCity插件
	 * @param int $quantity 购买的商品总是
	 * @param int express 运输方式 如：express\ems\self
	 * @return [json] [根据模板ID返回模板详细JSON字符串]
	 */
	public function delivery(){
		$id = I('get.id',0,'intval');
		$areaid = I('get.areaid',0,'intval');
		$quantity = I('get.quantity',0,'intval');
		$express = I('get.express','','text');

		if($id==0 || empty($id)){//id为空或为0
			$data['delivery_fee']=0;
		}else{
			$address = $this->user_address_model->get_user_address_by_id($areaid);
			if($express){
				$address['delivery'] = $express;
			}
			$delivery_fee = $this->order_logic->calc_delivery_fee($id,$address, $quantity);
			//组装DATA数据
			$data['delivery_fee']=$delivery_fee;
		}
		
		//组装JSON返回数据
		if(isset($delivery_fee)){
			$result['status']=1;
			$result['info'] = 'success';
			$result['data'] = $data;
		}else{
			$result['status']=0;
			$result['info'] = 'error';
		}
		$this->ajaxReturn($result,'JSON');
	}
	/**
	 * 用户收货地址列表json接口
	 * 
	 */
	public function address(){
		$this->init_user();
		$map['user_id'] = is_login();
		list($list,$totalCount) = $this->user_address_model->get_user_address_list($map);
		$first = 0;
		foreach($list as &$val){
            $val['province'] = D('district')->where(array('id' => $val['province']))->getField('name');
            $val['city'] = D('district')->where(array('id' => $val['city']))->getField('name');
            $val['district'] = D('district')->where(array('id' => $val['district']))->getField('name');

            if($val['modify_time']>$first){
            	$first=$val['modify_time'];
            	$val['first']=1;
            }else{
            	unset($val['first']);
            }
		}
		unset($val);

		//组装JSON返回数据
		if(isset($list)){
			$result['status']=1;
			$result['info'] = 'success';
			$result['data'] = $list;
		}else{
			$result['status']=0;
			$result['info'] = 'error';
		}
		$this->ajaxReturn($result,'JSON');
	}
	/**
	 * 根据ID获取优惠卷信息
	 * @return json 优惠券的详细信息
	 */
	public function coupon(){
		$id = I('get.id',0,'intval');
		$ret = $this->user_coupon->get_user_coupon_by_id($id);
		$ret['info']['rule']['discount'] = sprintf("%01.2f", $ret['info']['rule']['discount']/100);//将金额单位分转成元
		//组装JSON返回数据
		if($ret){
			$result['status']=1;
			$result['info'] = 'success';
			$result['data'] = $ret;
		}else{
			$result['status']=0;
			$result['info'] = 'error';
		}
		$this->ajaxReturn($result,'JSON');
	}

	/*
	 * 取消订单
	 */
	public function cancel_order()
	{
		if (IS_POST &&  _need_login()){
			if (!($order_id = I('id', 0, 'intval'))
				|| !($order = $this->order_model->get_order_by_id($order_id))
				|| !($order['user_id'] == is_login())
			){
				$result['status']=0;
				$result['info'] = '参数错误';
			}
			$ret = $this->order_logic->cancal_order($order);
			if ($ret){
				$result['status']=1;
				$result['info'] = '订单取消成功';
				$result['url'] = U('Muushop/user/orders');
			}else{
				$result['status']=0;
				$result['info'] = '取消失败';
			}
		}else{
			$result['status']=0;
			$result['info'] = '提交方式错误或未登陆';
		}
		$this->ajaxReturn($result,'JSON');
	}

	/*
	 * 确认收货
	 */
	public function do_receipt()
	{
		if (IS_POST &&  _need_login()){
			if (!($order_id = I('id', false, 'intval')) || !($order = $this->order_model->get_order_by_id($order_id)) || !($order['user_id'] == $this->user_id)){
				$result['status']=0;
				$result['info'] = '参数错误';
			}
			$ret = $this->order_logic->recv_goods($order);
			if ($ret){
				$result['status']=1;
				$result['info'] = '确认成功';
				$result['data'] = $ret;
				$result['url'] = U('Muushop/user/orders');
			}else{
				$result['status']=0;
				$result['info'] = '确认失败';
			}

		}else{
			$result['status']=0;
			$result['info'] = '提交方式错误或未登陆';
		}
		$this->ajaxReturn($result,'JSON');
	}
	/**
	 * 订单数据
	 * 参数 如：status=1;status=1,2,3,4查询不同状态订单
	 * @return [type] [description]
	 */
	public function orders(){
		$this->init_user();
		$page = I('get.page',1,'intval');
		$r = 20;
		$option['user_id'] = _need_login();
		$option['status'] = I('get.status','0','text');//获取订单状态的数字
		if(is_numeric($option['status'])){
			$option['status'] = $option['status'];
		}else{
			$tempArr = explode(',',$option['status']);
			$option['status']= array();
			foreach($tempArr as $v){
				$option['status'][] = array('eq',$v);
			}
			$option['status'][] = 'or';
		}

		list($order_list,$totalCount) = $this->order_model->get_order_list_by_page($option,$page,$order='create_time desc',$field='*',$r);
		$order_list = empty($order_list)?array(): $order_list;
		array_walk($order_list,function(&$a)
		{
			empty($a['products']) ||
			array_walk($a['products'],function(&$b)
			{
				$b['main_img'] = (empty($b['main_img'])?'':pic($b['main_img']));
			});
		});
		unset($a);

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
			$result['status']=1;
			$result['info'] = 'success';
			$result['data'] = array('list'=>$order_list,'totalCount'=>$totalCount);
		}else{
			$result['status']=0;
			$result['info'] = 'error';
		}
		$this->ajaxReturn($result,'JSON');
	}


	/**
	 * 订单评论
	 * @param  [type] $id [商品ID]
	 * @return [type]     [description]
	 */
	public function comment($page = 1, $r = 20)
	{
		//评价嗮图
		$product_id = I('get.product_id',0,'intval');
		$map['status'] = 1;
		$map['product_id'] = $product_id;

		$comment = $this->product_comment_model->get_list_by_page($map,$page,'create_time desc','*',$r);

		foreach($comment['list'] as &$val){
			if(empty($val['brief'])){
				$val['brief'] = '此用户未填写评价内容';
			}
			$val['sku'] = explode(';',$val['sku_id']);
			unset($val['sku'][0]);
			$val['sku'] = array_values($val['sku']);
			$val['create_time'] = friendlyDate($val['create_time']);
			//读取图片
			if($val['images']){
                $img_arr = explode(',',$val['images']);
                foreach ($img_arr as $k) {
                    $val['images_small_list'][] = getThumbImageById($k,100,100);
                    $val['images_big_list'][] = getThumbImageById($k,600,600);
                }
            }
		}
		unset($val);

		if($comment){
			$result['status']=1;
			$result['info'] = 'success';
			$result['data'] = $comment;
		}else{
			$result['status']=0;
			$result['info'] = 'error';
		}
		$this->ajaxReturn($result,'JSON');
	}


}