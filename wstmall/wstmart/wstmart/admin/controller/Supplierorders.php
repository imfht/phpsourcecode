<?php
namespace wstmart\admin\controller;
use wstmart\admin\model\SupplierOrders as M;
use wstmart\common\model\Payments as P;


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
 * 订单控制器
 */
class Supplierorders extends Base{
	/**
	 * 订单列表
	 */
    public function index(){
    	$areaList = model('areas')->listQuery(0); 
    	$this->assign("areaList",$areaList);
    	$this->assign("userId",(int)input('userId'));
    	$this->assign("p",(int)input("p"));
    	return $this->fetch("list");
    }
    /**
     * 获取分页
     */
    public function pageQuery(){
        $m = new M();
        return WSTGrid($m->pageQuery((int)input('orderStatus',10000)));
    }
   /**
    * 获取订单详情
    */
    public function view(){
        $m = new M();
        $p = new P();
        $rs = $m->getByView(Input("id/d",0));
        $this->assign("object",$rs);
        $this->assign("from",input("from/d",0));
        $this->assign("payMents",$p->getOnlinePayments());
        $this->assign("p",(int)input("p"));
        $this->assign("src",input("src"));
        return $this->fetch("view");
    }
    /**
     * 导出订单
     */
    public function toExport(){
    	$m = new M();
    	$rs = $m->toExport();
    	$this->assign('rs',$rs);
    }
    /**
     * 修改订单为已支付
     */
    public function changePayStatus(){
        $m = new M();
        return $m->changePayStatus();
    }

    /**
     * 强制修改订单状态
     */
    public function changeOrderStatus(){
        $m = new M();
        $p = new P();
        $this->assign("payMents",$p->getOnlinePayments());
        $this->assign("object",$m->getByView(input("id/d",0)));
        $this->assign("p",(int)input("p"));
        return $this->fetch("change");
    }
    /**
     * 修改订单
     */
    public function changeOrder(){
        $m = new M();
        return $m->changeOrderStatus();
    }
}
