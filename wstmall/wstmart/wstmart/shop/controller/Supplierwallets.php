<?php
namespace wstmart\shop\controller;
use wstmart\shop\model\SupplierOrders as OM;
use wstmart\common\model\Users as UM;
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
class SupplierWallets extends Base{
	/**
	 * 生成支付代码
	 */
	function getWalletsUrl(){
		$pkey = input('pkey');
        $data = [];
        $data['status'] = 1;
        $data['url'] = url('shop/supplierwallets/payment','pkey='.$pkey,'html',true);
		return $data;
	}
	
	/**
	 * 跳去支付页面
	 */
	public function payment(){
		if((int)session('WST_USER.userId')==0){
			$this->assign('message',"对不起，您尚未登录，请先登录!");
            return $this->fetch('supplier/error_msg');
		}
		$userId = (int)session('WST_USER.userId');
		$shopId = (int)session('WST_USER.shopId');
		$m = new UM();
		$user = $m->getFieldsById($userId,["payPwd"]);
		$this->assign('hasPayPwd',($user['payPwd']!="")?1:0);
		$pkey = input('pkey');
		$this->assign('pkey',$pkey);
        $pkey = WSTBase64urlDecode($pkey);
        $pkey = explode('@',$pkey);
        $data = [];
        $data['orderNo'] = $pkey[0];
        $data['isBatch'] = (int)$pkey[1];
        $data['userId'] = $userId;
        $data['shopId'] = $shopId;
		$m = new OM();
		$rs = $m->getOrderPayInfo($data);
		if(empty($rs)){
			$this->assign('message',"您的订单已支付，请勿重复支付~");
            return $this->fetch('supplier/error_msg');
		}else{
			$this->assign('needPay',$rs['needPay']);
			//获取用户钱包
			$shop = model('shops')->getFieldsById($shopId,'shopMoney');
			$this->assign('shopMoney',$shop['shopMoney']);
	        return $this->fetch('supplier/order_pay_wallets');
	    }
	}

	/**
	 * 钱包支付
	 */
	public function payByWallet(){
		$m = new OM();
        return $m->payByWallet();
	}

}
