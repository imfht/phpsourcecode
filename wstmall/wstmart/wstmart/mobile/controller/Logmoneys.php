<?php
namespace wstmart\mobile\controller;
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
 * 资金流水控制器
 */
class Logmoneys extends Base{
	// 前置方法执行列表
    protected $beforeActionList = [
        'checkAuth'
    ];
	/**
     * 查看用户资金流水
     */
	public function usermoneys(){
		$userId = (int)session('WST_USER.userId');
		$rs = model('Users')->getFieldsById($userId,['lockMoney','userMoney','rechargeMoney','payPwd']);
		$rs['isSetPayPwd'] = ($rs['payPwd']=='')?0:1;
        unset($rs['payPwd']);
		$rs['num'] = count(model('cashConfigs')->listQuery(0,$userId));
		$this->assign('rs',$rs);
		return $this->fetch('users/logmoneys/list');
	}
	/**
	 * 资金流水
	 */
	public function record(){
		$userId = (int)session('WST_USER.userId');
		$rs = model('Users')->getFieldsById($userId,['lockMoney','userMoney','rechargeMoney','payPwd']);
		$this->assign('rs',$rs);
		return $this->fetch('users/logmoneys/record');
	}
	/**
	 * 列表
	 */
	public function pageQuery(){
		$userId = (int)session('WST_USER.userId');
		$data = model('LogMoneys')->pageQuery("",$userId);
		return WSTReturn("", 1,$data);
	}
	/**
	* 验证支付密码
	*/
	public function checkPayPwd(){
		return model('mobile/users')->checkPayPwd();
	}

	/**
     * 充值[用户]
     */
    public function toRecharge(){
    	if((int)WSTConf('CONF.isOpenRecharge')==0)return;
    	$userId = (int)session('WST_USER.userId');
    	$rs = model('Users')->getFieldsById($userId,'userMoney');
    	$this->assign('rs',$rs);
    	$payments = model('common/payments')->recharePayments('2');
    	$this->assign('payments',$payments);
    	$chargeItems = model('common/ChargeItems')->queryList();
    	$this->assign('chargeItems',$chargeItems);
    	return $this->fetch('users/recharge/recharge');
    }
}
