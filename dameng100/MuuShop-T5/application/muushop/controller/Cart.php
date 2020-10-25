<?php

namespace app\muushop\controller;

use think\Controller;

class Cart extends Base {

	protected $cart_model;
	function _initialize()
	{
		$this->cart_model = model('muushop/MuushopCart');
		parent::init_user();
		parent::_initialize();
	}
	/**
	 * 购物车
	 * @return [type] [description]
	 */
	public function index()
	{
		$list = $this->cart_model->getListByUid(get_uid());
		foreach($list as &$val){
            $val['product']['price'] = sprintf("%01.2f", $val['product']['price']/100);//将金额单位分转成元
            $val['product']['ori_price'] = sprintf("%01.2f", $val['product']['ori_price']/100);
            $val['total_price'] = $val['product']['price']*$val['quantity'];
            $val['total_price'] = sprintf("%01.2f", $val['total_price']);
		}
		unset($val);
		
		$this->assign('list', $list);
		return $this->fetch();
	}
}