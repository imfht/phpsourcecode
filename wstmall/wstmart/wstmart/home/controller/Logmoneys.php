<?php
namespace wstmart\home\controller;
use wstmart\common\model\LogMoneys as M;
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
    protected $beforeActionList = ['checkAuth'];
    /**
     * 查看用户资金流水
     */
    public function usermoneys(){
        $rs = model('Users')->getFieldsById((int)session('WST_USER.userId'),['lockMoney','userMoney','rechargeMoney']);
        $this->assign('object',$rs);
        return $this->fetch('users/logmoneys/list');
    }

    /**
     * 获取用户数据
     */
    public function pageUserQuery(){
        $userId = (int)session('WST_USER.userId');
        $data = model('logMoneys')->pageQuery(0,$userId);
        return WSTReturn("", 1,$data);
    }
    
    /**
     * 充值[用户]
     */
    public function toUserRecharge(){
        if((int)WSTConf('CONF.isOpenRecharge')==0)return;
    	$payments = model('common/payments')->recharePayments('1');
    	$this->assign('payments',$payments);
    	$chargeItems = model('common/ChargeItems')->queryList();
    	$this->assign('chargeItems',$chargeItems);
    	return $this->fetch('users/recharge/pay_step1');
    }
}
