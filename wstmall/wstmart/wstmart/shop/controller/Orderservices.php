<?php
namespace wstmart\shop\controller;
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
class Orderservices extends Base{
    protected $beforeActionList = ['checkAuth'];
    
    // 商家发货
    public function shopSend(){
        $rs = model('common/OrderServices')->shopSend();
        return $rs;
    }
    // 商家确认收货
    public function shopReceive(){
        $rs = model('common/OrderServices')->shopReceive();
        return $rs;
    }
    // 售后列表查询
    public function pageQuery(){
        $rs = model('common/OrderServices')->pageQuery(1);
        return WSTReturn('ok', 1, $rs);
    }
    /**
     * 处理退款
     */
    public function dealRefund(){
        $rs = model('common/OrderServices')->dealRefund();
        return $rs;
    }
    /**
     * 处理售后申请
     */
    public function dealApply(){
        $rs = model('common/OrderServices')->dealApply();
        return $rs;
    }
    /**
     * 处理售后申请页
     */
    public function deal(){
        $object = model('common/OrderServices')->getDetail(1);
        // 等待卖家发货
        if($object['serviceStatus']==3){
            // 取出快递公司
            $express = model('Express')->listQuery();
		    $this->assign('express',$express);
        }
        return $this->fetch('orderservices/deal',['object'=>$object,'id'=>(int)input('id'),'p'=>(int)input('p')]);
    }
    /**
    * 售后申请列表
    */
    public function index(){
        // $this->assign('object',$m->getShopCfg((int)session('WST_USER.shopId')));
        return $this->fetch('orderservices/list',['p'=>(int)input('p')]);
    }

}
