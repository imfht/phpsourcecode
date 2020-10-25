<?php

namespace Muushop\Controller;

use Think\Controller;
class CartController extends BaseController {

	protected $cart_model;
	function _initialize()
	{
		parent::_initialize();
		$this->cart_model = D('Muushop/MuushopCart');
		parent::init_user();

	}

	public function index()
	{
		parent::init_user();
		$cart = $this->cart_model->get_shop_cart_by_user_id(get_uid());
		foreach($cart as &$val){
            $val['product']['price'] = sprintf("%01.2f", $val['product']['price']/100);//将金额单位分转成元
            $val['product']['ori_price'] = sprintf("%01.2f", $val['product']['ori_price']/100);
            $val['total_price'] = $val['product']['price']*$val['quantity'];
            $val['total_price'] = sprintf("%01.2f", $val['total_price']);
		}
		//dump($cart);exit;
		$this->assign('cart', $cart);
		$this->display();
	}
	/**
	 * ajax获取用户购物车数量
	 * @return [type] [description]
	 */
	public function count(){
			$totalCount = $this->cart_model->get_shop_cart_count_by_user_id(get_uid());
			if($totalCount){
				$result['status']=1;
				$result['info']='success';
				$result['data']=$totalCount;
			}else{
				$result['status']=0;
				$result['info']='error';
			}
			$this->ajaxReturn($result);
	}
	/**
	*加入购物车
	**/
	public function add_to_cart()
	{
		if(IS_POST){
			if (!($shop_cart = $this->cart_model->create())){
				$this->error($this->cart_model->getError());
			}
			$shop_cart['user_id'] = is_login();
			$ret = $this->cart_model->add_shop_cart($shop_cart);
			if ($ret){
				$this->success('加入购物车成功');
			}else{
				$this->error('加入购物车时发生错误');
			}
		}
		
	}
	public function delete_cart()
	{
		$ids = I('ids','');
		$ret = $this->cart_model->delete_shop_cart($ids, get_uid());
		if ($ret){
			$this->success('商品删除成功',U('Muushop/cart/index'));
		}else{
			$this->error('商品删除时发生错误');
		}
	}

	public function edit_to_cart()
	{
		if(IS_POST){
			$data = I('post.data','','text');
			if (!($shop_cart = $this->cart_model->create())){
				$this->error($this->cart_model->getError());
			}
			$shop_cart = $data;
			$shop_cart['user_id'] = get_uid();

			$ret = $this->cart_model->add_shop_cart($shop_cart);
			if ($ret){
				$this->success('成功修改购物车数据');
			}else{
				$this->error('修改数据时发生错误');
			}
		}
	}

}