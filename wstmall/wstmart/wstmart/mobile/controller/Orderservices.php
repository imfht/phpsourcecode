<?php
namespace wstmart\mobile\controller;
use wstmart\common\model\OrderServices as M;
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
 * 商家入驻控制器
 */
class Orderservices extends Base{
    protected $beforeActionList = ['checkAuth'];
    /**
    * 售后申请页面
    */
    public function apply(){
        $data = input('param.');
        $m = new M();
        $goods = $m->getGoods();
        // 退换货原因
        $reasons = WSTDatas('ORDER_SERVICES');
        $this->assign(['goods'=>$goods, 'reasons'=>$reasons, 'orderId'=>(int)input('orderId')]);
        return $this->fetch('users/orderservices/apply');
    }
    /**
     * 提交售后申请
     */
    public function commit(){
        $m = new M();
        return $m->commit();
    }
    /**
     * 提交售后申请
     */
    public function oslist(){
        return $this->fetch('users/orderservices/list');
    }
    /**
     * 列表查询
     */
    public function pagequery(){
        $m = new M();
        return $m->pageQuery();
    }

    /**
     * 详情页
     */
    public function detail(){
        $m = new M();
        $detail = $m->getDetail();
        $log = $m->getLog();
        return $this->fetch('users/orderservices/detail',['detail'=>$detail,'log'=>$log,'id'=>(int)input('id')]);
    }
    /**
     * 用户发货页
     */
    public function sendPage(){
        $m = new M();
        $detail = $m->getDetail();
        // 等待买家发货
        if($detail['serviceStatus']==1){
            // 取出快递公司
            $express = model('Express')->listQuery();
		    $this->assign('express',$express);
        }
        return $this->fetch('users/orderservices/send',['detail'=>$detail, 'id'=>(int)input('id')]);
    }
    /**
     * 用户发货
     */
    public function userExpress(){
        $m = new M();
        return $m->userExpress();
    }
    /**
     * 用户确认收货
     */
    public function userReceive(){
        $m = new M();
        return $m->userReceive();
    }
    /**
     * 获取当前可退款金额
     */
    public function getRefundableMoney(){
        $m = new M();
        return $m->getRefundableMoney();
    }
}
