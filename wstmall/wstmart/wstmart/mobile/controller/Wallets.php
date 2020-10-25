<?php
namespace wstmart\mobile\controller;
use wstmart\common\model\Orders as OM;
/**
 * ============================================================================
 * WSTMart多用户商城
 * 版权所有 2016-2066 广州商淘信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.wstmart.net
 * 交流社区:http://bbs.shangtao.net
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 余额控制器
 */
class Wallets extends Base{
	// 前置方法执行列表
	protected $beforeActionList = [
			'checkAuth'
	];
	/**
	 * 跳去支付页面
	 */
	public function payment(){
		$pkey = WSTBase64urlDecode(input("pkey"));
		$pkey = explode('@',$pkey);
		$orderNo = $pkey[0];
		$isBatch = (int)$pkey[1];
        $data = [];
        $userId = (int)session('WST_USER.userId');

        $data['orderNo'] = $orderNo;
        $data['isBatch'] = $isBatch;
        $data['userId'] = $userId;
        $this->assign('data',$data);
		$m = new OM();
		$rs = $m->getOrderPayInfo($data);
		
		$list = $m->getByUnique($userId,$orderNo,$isBatch);
		$this->assign('rs',$list);

		if(empty($rs) || $rs['needPay']<=0){
			$this->assign('type','');
			return $this->fetch("users/orders/orders_list");
		}else{
			$this->assign('needPay',$rs['needPay']);
			//获取用户钱包
			$user = model('users')->getFieldsById($data['userId'],'userMoney,payPwd');
			$this->assign('userMoney',$user['userMoney']);
        	$payPwd = $user['payPwd'];
        	$payPwd = empty($payPwd)?0:1;
			$this->assign('payPwd',$payPwd);
	    }
	    return $this->fetch('users/orders/orders_pay_wallets');
	}
	/**
	 * 钱包支付
	 */
	public function payByWallet(){
		$m = new OM();
		return $m->payByWallet();
	}
}
