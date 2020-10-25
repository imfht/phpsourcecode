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
 * 提现记录控制器
 */
class Cashdraws extends Base{
    // 前置方法执行列表
    protected $beforeActionList = [
        'checkAuth',
    ];
	/**
     * 查看用户提现记录
     */
	public function index(){
		return $this->fetch('users/cashdraws/list');
	}

	/**
     * 获取用户数据
     */
    public function pageQuery(){
        $userId = (int)session('WST_USER.userId');
        $data = model('CashDraws')->pageQuery(0,$userId);
        return WSTReturn("", 1,$data);
    }

    /**
     * 提现
     */ 
    public function drawMoney(){
        return model('CashDraws')->drawMoney();
    }
}
