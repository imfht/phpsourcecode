<?php
namespace wstmart\home\controller;
use wstmart\common\model\Orders as OM;
use wstmart\common\model\Shops as SM;
use wstmart\common\model\Suppliers as SUM;
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
class Wallets extends Base{
	/**
	 * 生成支付代码
	 */
	function getWalletsUrl(){
        $payObj = input("payObj",'');
        $flowId = input('flowId');
        $pkey = input('pkey');
        $data = [];
        $data['status'] = 1;
        if($payObj=='enter'){
            $data['url'] = url('home/wallets/shopEnterPayment','pkey='.$pkey.'&flowId='.$flowId,'html',true);
        }elseif($payObj=='supplier_enter') {
            $data['url'] = url('home/wallets/supplierEnterPayment','pkey='.$pkey.'&flowId='.$flowId,'html',true);
        }else{
            $data['url'] = url('home/wallets/payment','pkey='.$pkey,'html',true);
        }


		return $data;
	}
	
	/**
	 * 跳去支付页面
	 */
	public function payment(){
		if((int)session('WST_USER.userId')==0){
			$this->assign('message',"对不起，您尚未登录，请先登录!");
            return $this->fetch('error_msg');
		}
		$userId = (int)session('WST_USER.userId');
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
		$m = new OM();
		$rs = $m->getOrderPayInfo($data);
		if(empty($rs)){
			$this->assign('message',"您的订单已支付，请勿重复支付~");
            return $this->fetch('error_msg');
		}else{
			$this->assign('needPay',$rs['needPay']);
			//获取用户钱包
			$user = model('users')->getFieldsById($data['userId'],'userMoney');
			$this->assign('userMoney',$user['userMoney']);
	        return $this->fetch('order_pay_wallets');
	    }
	}

	/**
	 * 钱包支付
	 */
	public function payByWallet(){
		$m = new OM();
        return $m->payByWallet();
	}

    /**
     * 跳去店铺入驻支付页面
     */
    public function shopEnterPayment(){
        if((int)session('WST_USER.userId')==0){
            $this->assign('message',"对不起，您尚未登录，请先登录!");
            return $this->fetch('error_msg');
        }
        $userId = (int)session('WST_USER.userId');
        $m = new UM();
        $user = $m->getFieldsById($userId,["payPwd"]);
        $this->assign('hasPayPwd',($user['payPwd']!="")?1:0);
        $pkey = input('pkey');
        $this->assign('pkey',$pkey);
        $pkey = WSTBase64urlDecode($pkey);
        $pkey = explode('@',$pkey);
        $needPay = $pkey[0];
        $this->assign('needPay',$needPay);
        //获取用户钱包
        $user = model('users')->getFieldsById($userId,'userMoney');
        $this->assign('userMoney',$user['userMoney']);
        $flowId = input('flowId');
        $this->assign('flowId',$flowId);
        $this->assign('payStep',2);
        $this->checkStep($flowId);
        $shopFlows = model('shops')->getShopFlowDatas($flowId);
        $stepFields = model('shops')->getFlowFieldsById($flowId);
        $this->assign('shopFlows',$shopFlows['flows']);
        $this->assign('prevStep',$shopFlows['prevStep']);
        $this->assign('currStep',$shopFlows['currStep']);
        $this->assign('nextStep',$shopFlows['nextStep']);
        $this->assign('stepFields',$stepFields);
        $apply = model('shops')->getShopApply();
        $this->assign('apply',$apply);
        $this->assign('payType','wallets');
        return $this->fetch('shop_join_step');
    }

    /**
     * 钱包支付
     */
    public function shopEnterPayByWallet(){
        $m = new SM();
        $pkey = WSTBase64urlDecode(input('pkey'));
        $pkey = explode('@',$pkey);
        $obj = array ();
        $obj["orderNo"] = WSTOrderNo();
        $obj["targetId"] = (int)session('WST_USER.userId');
        $obj["targetType"] = 0;
        $obj["total_fee"] = $pkey[0];
        $obj["scene"] = 'enter';
        return $m->payByWallet($obj);
    }

    /**
     * 跳去供货商入驻支付页面
     */
    public function supplierEnterPayment(){
        if((int)session('WST_USER.userId')==0){
            $this->assign('message',"对不起，您尚未登录，请先登录!");
            return $this->fetch('error_msg');
        }
        $userId = (int)session('WST_USER.userId');
        $m = new UM();
        $user = $m->getFieldsById($userId,["payPwd"]);
        $this->assign('hasPayPwd',($user['payPwd']!="")?1:0);
        $pkey = input('pkey');
        $this->assign('pkey',$pkey);
        $pkey = WSTBase64urlDecode($pkey);
        $pkey = explode('@',$pkey);
        $needPay = $pkey[0];
        $this->assign('needPay',$needPay);
        //获取用户钱包
        $user = model('users')->getFieldsById($userId,'userMoney');
        $this->assign('userMoney',$user['userMoney']);
        $flowId = input('flowId');
        $this->assign('flowId',$flowId);
        $this->assign('payStep',2);
        $this->supplierCheckStep($flowId);
        $supplierFlows = model('suppliers')->getSupplierFlowDatas($flowId);
        $stepFields = model('suppliers')->getFlowFieldsById($flowId);
        $this->assign('supplierFlows',$supplierFlows['flows']);
        $this->assign('prevStep',$supplierFlows['prevStep']);
        $this->assign('currStep',$supplierFlows['currStep']);
        $this->assign('nextStep',$supplierFlows['nextStep']);
        $this->assign('stepFields',$stepFields);
        $apply = model('suppliers')->getSupplierApply();
        $this->assign('apply',$apply);
        $this->assign('payType','wallets');
        return $this->fetch('suppliers/supplier_join_step');
    }

    /**
     * 钱包支付
     */
    public function supplierEnterPayByWallet(){
        $m = new SUM();
        $pkey = WSTBase64urlDecode(input('pkey'));
        $pkey = explode('@',$pkey);
        $obj = array ();
        $obj["orderNo"] = WSTOrderNo();
        $obj["targetId"] = (int)session('WST_USER.userId');
        $obj["targetType"] = 0;
        $obj["total_fee"] = $pkey[0];
        $obj["scene"] = 'enter';
        return $m->payByWallet($obj);
    }

    /**
     * 检测入驻商城时有步骤有没有遗漏，不允许跳过步骤
     */
    public function checkStep($flowId){
        $this->checkUserType('shop');
        if((int)WSTConf('CONF.isOpenShopApply')!=1)return;
        $tmpShopApplyFlow = session('tmpShopApplyFlow');
        $tmpApplyStep = (int)session('tmpApplyStep');
        //如果没有建立数组则强制重新开始
        if(!$tmpShopApplyFlow){
            return $this->redirect(Url('home/shops/join'));
        }
        $flowSteps = [];
        $isFind = false;
        foreach ($tmpShopApplyFlow as $key => $v) {
            $flowSteps[] = $v['flowId'];
            if($v['flowId']==$tmpApplyStep){
                $isFind = true;
                break;
            }
        }
        //没找到这个环节强制重新开始
        if(!$isFind){
            $this->redirect(Url('home/shops/joinStepNext',array('id'=>$tmpShopApplyFlow[0]['flowId'])));
            exit();
        }
        //如果找到则判断是否当前环节是否有效
        if(!in_array($flowId,$flowSteps)){
            $flowId = end($flowSteps);
            $this->redirect(Url('home/shops/joinStepNext',array('id'=>$flowId)));
            exit();
        }
    }

    /**
     * 检测供货商入驻商城时有步骤有没有遗漏，不允许跳过步骤
     */
    public function supplierCheckStep($flowId){
        $this->checkUserType('supplier');
        if((int)WSTConf('CONF.isOpenSupplierApply')!=1)return;
        $tmpSupplierApplyFlow = session('tmpSupplierApplyFlow');
        $tmpApplyStep = (int)session('tmpApplyStep');
        //如果没有建立数组则强制重新开始
        if(!$tmpSupplierApplyFlow){
            return $this->redirect(Url('home/suppliers/join'));
        }
        $flowSteps = [];
        $isFind = false;
        foreach ($tmpSupplierApplyFlow as $key => $v) {
            $flowSteps[] = $v['flowId'];
            if($v['flowId']==$tmpApplyStep){
                $isFind = true;
                break;
            }
        }
        //没找到这个环节强制重新开始
        if(!$isFind){
            $this->redirect(Url('home/suppliers/joinStepNext',array('id'=>$tmpSupplierApplyFlow[0]['flowId'])));
            exit();
        }
        //如果找到则判断是否当前环节是否有效
        if(!in_array($flowId,$flowSteps)){
            $flowId = end($flowSteps);
            $this->redirect(Url('home/suppliers/joinStepNext',array('id'=>$flowId)));
            exit();
        }
    }

    // 检测用户账号
    public function checkUserType($type=''){
        $USER = session('WST_USER');
        if($type=='shop'){
            if(!($USER['userType']==0 || $USER['userType']==1)){
                if(request()->isAjax()){
                    die('{"status":-999,"msg":"当前账号已关联供货商/门店信息，不能申请商家"}');
                }else{
                    $this->redirect('home/shops/disableApply');
                    exit;
                }
            }
        }else{
            if(!($USER['userType']==0 || $USER['userType']==3)){
                if(request()->isAjax()){
                    die('{"status":-999,"msg":"当前账号已关联店铺/门店信息，不能申请供货商"}');
                }else{
                    $this->redirect('home/suppliers/disableApply');
                    exit;
                }
            }
        }
    }
}
