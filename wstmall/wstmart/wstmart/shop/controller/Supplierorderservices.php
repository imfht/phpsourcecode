<?php
namespace wstmart\shop\controller;
use wstmart\common\model\SupplierOrderServices as M;
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
 * 售后控制器
 */
class Supplierorderservices extends Base{
    protected $beforeActionList = ['checkAuth'];
    /**
     * 用户确认收货
     */
    public function userReceive(){
        $m = new M();
        $shopId = (int)session('WST_USER.shopId');
        $userId = (int)session('WST_USER.userId');
        return $m->userReceive($shopId,$userId);
    }
    /**
     * 用户发货
     */
    public function userExpress(){
        $m = new M();
        $shopId = (int)session('WST_USER.shopId');
        $userId = (int)session('WST_USER.userId');
        return $m->userExpress($shopId,$userId);
    }
    /**
     * 售后详情
     */
    public function detail(){
        $m = new M();
        $shopId = (int)session('WST_USER.shopId');
        $detail = $m->getDetail(0,$shopId,0);
        $log = $m->getLog($shopId);
        // 等待买家发货
        if($detail['serviceStatus']==1){
            // 取出快递公司
            $express = model('Express')->listQuery();
		    $this->assign('express',$express);
        }
        return $this->fetch('supplier/orderservices/detail',['detail'=>$detail,'log'=>$log,'id'=>(int)input('id')]);
    }
    /**
	* 售后列表查询
	*/
    public function pagequery(){
        $m = new M();
        $shopId = (int)session('WST_USER.shopId');
        $rs = $m->pageQuery(0,$shopId,0);
        return WSTReturn('ok',1,$rs);
    }
    /**
	* 售后申请页
	*/
    public function oslist(){
        $m = new M();
        $shopId = (int)session('WST_USER.shopId');
        $rs = $m->getGoods($shopId);
        return $this->fetch('supplier/orderservices/list',['p'=>(int)input('p')]);
    }
	/**
	* 售后申请页
	*/
    public function index(){
        $m = new M();
        $shopId = (int)session('WST_USER.shopId');
        $rs = $m->getGoods($shopId);
        // 退换货原因
        $reasons = WSTDatas('ORDER_SERVICES');
        return $this->fetch('supplier/orderservices/apply',['rs'=>$rs,'reasons'=>$reasons,'orderId'=>(int)input('orderId')]);
    }

    /**
     * 提交售后申请
     */
    public function commit(){
        $m = new M();
        $shopId = (int)session('WST_USER.shopId');
        $userId = (int)session('WST_USER.userId');
        return $m->commit($shopId,$userId);
    }
    /**
     * 获取当前可退款金额
     */
    public function getRefundableMoney(){
        $m = new M();
        $shopId = (int)session('WST_USER.shopId');
        return $m->getRefundableMoney($shopId);
    }
}
