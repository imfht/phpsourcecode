<?php
namespace wstmart\common\model;
use think\Db;
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
 * 支付管理业务处理
 */
class Payments extends Base{
	/**
	 * 获取支付方式种类
	 *
	 * $isApp 如果是接口请求,则不返回payConfig数据
	 */
	public function getByGroup($payfor = '', $onlineType = -1, $isApp = false){
		$payments = ['0'=>[],'1'=>[]];
		$where = ['enabled'=>1];
		if(in_array($onlineType,[1,0]))$where['isOnline'] = $onlineType;
		$rs = $this->where($where)->where("find_in_set ($payfor,payFor)")->order('payOrder asc')->select();
		foreach ($rs as $key =>$v){
			if($v['payConfig']!='')$v['payConfig'] = json_decode($v['payConfig'], true);
			if($isApp)unset($v['payConfig']);
			$payments[$v['isOnline']][] = $v;
		}
		return $payments;
	}

	
	/**
	 * 获取支付信息
	 */
	public function getPayment($payCode){
		$payment = $this->where("enabled=1 AND payCode='$payCode' AND isOnline=1")->find();
		$payConfig = json_decode($payment["payConfig"]) ;
		foreach ($payConfig as $key => $value) {
			$payment[$key] = $value;
		}
		return $payment;
	}
	
	/**
	 * 获取在线支付方式
	 */
	public function getOnlinePayments(){
		//获取支付信息
		return $this->where(['isOnline'=>1,'enabled'=>1])->order('payOrder asc')->select();
	}
	/**
	 * 判断某种支付是否开启
	 */
	public function isEnablePayment($payCode){
        //获取支付信息
		return $this->where(['isOnline'=>1,'enabled'=>1,'payCode'=>$payCode])->Count();
	}
	
	public function recharePayments($payfor = ''){
		$rs = $this->where(['isOnline'=>1,'enabled'=>1])->where("find_in_set ($payfor,payFor)")->where("payCode!='wallets'")
			->field('id,payCode,payName,isOnline')->order('payOrder asc')->select();
		return $rs;
	}
}
