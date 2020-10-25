<?php
namespace wstmart\mobile\controller;
use wstmart\common\model\Carts as M;
use wstmart\common\model\UserAddress;
use wstmart\common\model\Payments;
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
 * 购物车控制器
 */
class Carts extends Base{

	// 前置方法执行列表
    protected $beforeActionList = [
        'checkAuth'
    ];
	/**
	 * 查看购物车列表
	 */
	public function index(){
		$m = new M();
		$carts = $m->getCarts(false);
		$this->assign('carts',$carts);
		return $this->fetch('carts');
	}
    /**
    * 加入购物车
    */
	public function addCart(){
		$m = new M();
		$rs = $m->addCart();
		$rs['cartNum'] = WSTCartNum();
		return $rs;
	}
	/**
	 * 修改购物车商品状态
	 */
	public function changeCartGoods(){
		$m = new M();
		$rs = $m->changeCartGoods();
		return $rs;
	}
	/**
	 * 批量修改购物车状态
	 */
	public function batchChangeCartGoods(){
		$m = new M();
		return $m->batchChangeCartGoods();
	}
	/**
	 * 删除购物车里的商品
	 */
	public function delCart(){
		$m = new M();
		$rs= $m->delCart();
		return $rs;
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
	 * 计算运费、积分和总商品价格/虚拟商品
	 */
	public function getQuickCartMoney(){
		$m = new M();
		$data = $m->getQuickCartMoney();
		return $data;
	}
	/**
	 * 跳去购物车结算页面
	 */
	public function settlement(){
		$m = new M();
		//获取一个用户地址
		$addressId = (int)input('addressId');
		$ua = new UserAddress();
		if($addressId>0){
			$userAddress = $ua->getById($addressId);
		}else{
			$userAddress = $ua->getDefaultAddress();
		}
		$this->assign('userAddress',$userAddress);
		//获取支付方式
		$pa = new Payments();
		$payments = $pa->getByGroup('2');
		//获取已选的购物车商品
		$carts = $m->getCarts(true);
		
		hook("mobileControllerCartsSettlement",["carts"=>$carts,"payments"=>&$payments]);
		
		$this->assign('payments',$payments);
		//获取用户积分
		$user = model('users')->getFieldsById((int)session('WST_USER.userId'),'userScore');
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
		
		$this->assign('carts',$carts);
		return $this->fetch('settlement');
	}
	/**
	 * 跳去虚拟商品购物车结算页面
	 */
	public function quickSettlement(){
		$m = new M();
		//获取支付方式
		$pa = new Payments();
		$payments = $pa->getByGroup('2');
		$this->assign('payments',$payments);
		//获取用户积分
		$user = model('users')->getFieldsById((int)session('WST_USER.userId'),'userScore');
		//获取已选的购物车商品
		$carts = $m->getQuickCarts();
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
		
		$this->assign('carts',$carts);
		return $this->fetch('settlement_quick');
	}

    /**
     * 将购物车里选择的商品移入我的关注
     */
    public function moveToFavorites(){
        $m = new M();
        $rs= $m->moveToFavorites();
        return $rs;
    }

    /**
     * 获取店铺自提点
     */
    public function getStores(){
    	$userId = (int)session('WST_USER.userId');
    	$rs = model("common/Stores")->shopStores($userId);
    	return WSTReturn("", 1,$rs);
    }
}
