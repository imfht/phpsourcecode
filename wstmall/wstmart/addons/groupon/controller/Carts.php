<?php
namespace addons\groupon\controller;

use think\addons\Controller;
use addons\groupon\model\Groupons as M;
use wstmart\common\model\UserAddress;
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
 * 团购商品插件
 */
class Carts extends Controller{
	public function __construct(){
		parent::__construct();
		$this->assign("v",WSTConf('CONF.wstVersion')."_".WSTConf('CONF.wstPCStyleId'));
	}

    /**
     * 下单
     */
    public function addCart(){
        $m = new M();
        return $m->addCart();
    }


	/**
	 * 结算页面
	 */
	public function settlement(){
	    $CARTS = session('GROUPON_CARTS'); 
		if(empty($CARTS)){
			header("Location:".addon_url('groupon://goods/lists')); 
			exit;
		}
		//获取一个用户地址
		$userAddress = model('common/UserAddress')->getDefaultAddress();
		$this->assign('userAddress',$userAddress);
		//获取省份
		$areas = model('common/Areas')->listQuery();
		$this->assign('areaList',$areas);
		$m = new M();
		$carts = $m->getCarts();
		$this->assign('carts',$carts);
		//获取用户积分
        $user = model('common/users')->getFieldsById((int)session('WST_USER.userId'),'userScore');
        $this->assign('userScore',$user['userScore']);
        //获取支付方式
		$onlineType = ($carts['goodsType']==1)?1:-1;
		$payments = model('common/payments')->getByGroup('1',$onlineType);
        $this->assign('payments',$payments);

		return $this->fetch("/home/index/settlement");
	}

	/**
	 * 计算运费、积分和总商品价格
	 */
	public function getCartMoney(){
		$m = new M();
		$data = $m->getCartMoney();
		return $data;
	}

	/**
	 * 下单
	 */
	public function submit(){
		$m = new M();
		$data = $m->submit((int)input('orderSrc'));
		return $data;
	}
    
	/**
	 * 微信结算页面
	 */
	public function wxSettlement(){
		$CARTS = session('GROUPON_CARTS');
		if(empty($CARTS)){
			header("Location:".addon_url('groupon://goods/wxlists'));
			exit;
		}
		//获取一个用户地址
		$addressId = (int)input('addressId');
		$ua = new UserAddress();
		if($addressId>0){
			$userAddress = $ua->getById($addressId);
		}else{
			$userAddress = $ua->getDefaultAddress();
		}
		$this->assign('userAddress',$userAddress);
		//获取省份
		$areas = model('common/Areas')->listQuery();
		$this->assign('areaList',$areas);
		$m = new M();
		$carts = $m->getCarts();
		$this->assign('carts',$carts);
		//获取用户积分
		$user = model('common/users')->getFieldsById((int)session('WST_USER.userId'),'userScore');
		//计算可用积分和金额
		$goodsTotalMoney = $carts['goodsTotalMoney'];
		$goodsTotalScore = WSTScoreToMoney($goodsTotalMoney,true);
		$useOrderScore =0;
		$useOrderMoney = 0;
		if($user['userScore']>$goodsTotalScore){
			$useOrderScore = $goodsTotalScore;
			$useOrderMoney = $goodsTotalMoney;
		}else{
			$useOrderScore = $user['userScore'];
			$useOrderMoney = WSTScoreToMoney($useOrderScore);
		}
		$this->assign('userOrderScore',$useOrderScore);
		$this->assign('userOrderMoney',$useOrderMoney);
		//获取支付方式
		$onlineType = ($carts['goodsType']==1)?1:-1;
		$payments = model('common/payments')->getByGroup('3',$onlineType);
		$this->assign('payments',$payments);
		return $this->fetch("/wechat/index/settlement");
	}
	
	/**
	 * 手机结算页面
	 */
	public function moSettlement(){
		$CARTS = session('GROUPON_CARTS');
		if(empty($CARTS)){
			header("Location:".addon_url('groupon://goods/molists'));
			exit;
		}
		//获取一个用户地址
		$addressId = (int)input('addressId');
		$ua = new UserAddress();
		if($addressId>0){
			$userAddress = $ua->getById($addressId);
		}else{
			$userAddress = $ua->getDefaultAddress();
		}
		$this->assign('userAddress',$userAddress);
		//获取省份
		$areas = model('common/Areas')->listQuery();
		$this->assign('areaList',$areas);
		$m = new M();
		$carts = $m->getCarts();
		$this->assign('carts',$carts);
		//获取用户积分
		$user = model('common/users')->getFieldsById((int)session('WST_USER.userId'),'userScore');
		//计算可用积分和金额
		$goodsTotalMoney = $carts['goodsTotalMoney'];
		$goodsTotalScore = WSTScoreToMoney($goodsTotalMoney,true);
		$useOrderScore =0;
		$useOrderMoney = 0;
		if($user['userScore']>$goodsTotalScore){
			$useOrderScore = $goodsTotalScore;
			$useOrderMoney = $goodsTotalMoney;
		}else{
			$useOrderScore = $user['userScore'];
			$useOrderMoney = WSTScoreToMoney($useOrderScore);
		}
		$this->assign('userOrderScore',$useOrderScore);
		$this->assign('userOrderMoney',$useOrderMoney);
		//获取支付方式
		$onlineType = ($carts['goodsType']==1)?1:-1;
		$payments = model('common/payments')->getByGroup('2',$onlineType);
		$this->assign('payments',$payments);
		return $this->fetch("/mobile/index/settlement");
	}
}