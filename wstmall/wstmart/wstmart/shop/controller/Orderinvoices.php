<?php
namespace wstmart\shop\controller;
use wstmart\common\model\OrderInvoices as M;
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
 * 发票详情控制器
 */

class OrderInvoices extends Base{
    protected $beforeActionList = ['checkAuth'];

    /******************************* 商家  ****************************************/
    /**
     * 商家-查看订单详情列表
     */

    public function shopInvoices(){
        $this->assign("shopId",(int)input('shopId'));
        $this->assign("p",(int)input("p"));
        return $this->fetch("orders/list_invoices");
    }

    /**
     * 获取商家订单详情列表
     */
    public function queryShopInvoicesByPage(){
        $rs = model('OrderInvoices')->queryShopInvoicesByPage();
        return WSTGrid($rs['data']);
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
     * 批量设置
     */
    public function setByBatch(){
        $m = new M();
        $rs = $m->setByBatch();
        return $rs;
    }

}
