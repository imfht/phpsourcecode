<?php
namespace wstmart\admin\controller;
use wstmart\admin\model\Settlements as M;
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
 * 结算控制器
 */
class Settlements extends Base{
    public function index(){
        $this->assign("p",(int)input("p"));
    	return $this->fetch('list');
    }

    /**
     * 获取列表
     */
    public function pageQuery(){
    	$m = new M();
    	return WSTGrid($m->pageQuery());
    }
    
    /**
     *  跳去结算详情
     */
    public function toView(){
    	$m = new M();
    	$object = $m->getById();
        $this->assign("p",(int)input("p"));
    	$this->assign("object",$object);
    	return $this->fetch('view');
    }

    /**
     * 获取订单商品
     */
    public function pageGoodsQuery(){
        $m = new M();
        return WSTGrid($m->pageGoodsQuery());
    }

    /*************************************************
     *          以下是平台主动生成结算单
     ************************************************/
    /**
     * 进入平台结算野蛮
     */
    public function toShopIndex(){
        $this->assign("areaList",model('areas')->listQuery(0));
        $this->assign("p",(int)input("p"));
        return $this->fetch('list_shop');
    }

    /**
     * 获取待结算的商家列表
     */
    public function pageShopQuery(){
        $m = new M();
        return WSTGrid($m->pageShopQuery());
    }
    /**
     * 进入订单列表页面
     */
    public function toOrders(){
        $this->assign("id",(int)input('id'));
        $this->assign("p",(int)input("p"));
        return $this->fetch('list_order');
    }
    /**
     * 获取商家的待结算订单列表
     */
    public function pageShopOrderQuery(){
        $m = new M();
        return WSTGrid($m->pageShopOrderQuery());
    }
    
    /**
     * 导出
     */
    public function toExport(){
        $m = new M();
        $rs = $m->toExport();
        $this->assign('rs',$rs);
    }

    /**
     * 结算佣金统计
     */
    public function statPageQuery(){
        $m = new M();
        return WSTGrid($m->statPageQuery());
    }
}
