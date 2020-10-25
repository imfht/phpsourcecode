<?php
namespace wstmart\mobile\controller;
use wstmart\common\model\OrderComplains as M;
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
 * 投诉控制器
 */
class orderComplains extends Base{
    // 前置方法执行列表
    protected $beforeActionList = [
        'checkAuth'
    ];
	public function complain(){
		$oId = (int)input('oId');
		$this->assign('oId',$oId);
		return $this->fetch('users/orders/orders_complains');
	}
	/**
     * 保存订单投诉信息
     */
    public function saveComplain(){
        return model('OrderComplains')->saveComplain();
    }
    /**
    * 用户投诉列表
    */
    public function index(){
    	return $this->fetch('users/orders/list_complains');
    }

    /**
    * 获取用户投诉列表
    */    
    public function complainByPage(){
        $m = model('OrderComplains');
        return $m->queryUserComplainByPage();
        
    }

    /**
     * 用户查投诉详情
     */
    public function getComplainDetail(){
        $rs = model('OrderComplains')->getComplainDetail(0);
        $annex = $rs['complainAnnex'];
        if($annex){
        	foreach($annex as $k=>$v){
        		$annex1[] = WSTImg($v,3);
        	}
        	$rs['complainAnnex'] = $annex1;
        }
        return $rs;
    }

}
