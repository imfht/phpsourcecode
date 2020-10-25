<?php
namespace wstmart\shop\controller;
use wstmart\shop\model\Reports as M;
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
 * 报表控制器
 */
class Reports extends Base{
    protected $beforeActionList = ['checkAuth'];
	/**
     * 商品销售排行
     */
    public function topSaleGoods(){
    	$this->assign("startDate",date('Y-m-d',strtotime("-1month")));
        $this->assign("endDate",date('Y-m-d'));
    	return $this->fetch('reports/top_sale_goods');
    }
    public function getTopSaleGoods(){
    	$n = new M();
        return WSTGrid($n->getTopSaleGoods());
    }
    /**
     * 获取销售额
     */
    public function statSales(){
    	$this->assign("startDate",date('Y-m-d',strtotime("-1month")));
        $this->assign("endDate",date('Y-m-d'));
        return $this->fetch('reports/stat_sales');
    }
    public function getStatSales(){
    	$m = new M();
        return $m->getStatSales();
    }

    /**
     * 获取销售订单
     */
    public function statOrders(){
        $this->assign("startDate",date('Y-m-d',strtotime("-1month")));
        $this->assign("endDate",date('Y-m-d'));
        return $this->fetch('reports/stat_orders');
    }
    public function getStatOrders(){
        $m = new M();
        return $m->getStatOrders();
    }

    /**
     * 导出商品销售排行Excel
     */
    public function toExportTopSaleGoods(){
        $m = new M();
        $rs = $m->toExportTopSaleGoods();
        $this->assign('rs',$rs);
    }
    /**
     * 导出销售额Excel
     */
    public function toExportStatSales(){
        $m = new M();
        $rs = $m->toExportStatSales();
        $this->assign('rs',$rs);
    }
    /**
     * 导出销售订单统计Excel
     */
    public function toExportStatOrders(){
        $m = new M();
        $rs = $m->toExportStatOrders();
        $this->assign('rs',$rs);
    }
}
