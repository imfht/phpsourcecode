<?php
namespace wstmart\admin\controller;
use wstmart\admin\model\LogMoneys as M;
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
 * 资金流水日志控制器
 */
class Logmoneys extends Base{
	
    public function index(){
        $this->assign("p",(int)input("p"));
    	return $this->fetch("list");
    }
    
    /**
     * 获取用户分页
     */
    public function pageQueryByUser(){
    	$m = new M();
    	return WSTGrid($m->pageQueryByUser());
    }
    /**
     * 获取商分页
     */
    public function pageQueryByShop(){
        $m = new M();
        return WSTGrid($m->pageQueryByShop());
    }
    /**
     * 获取供货商分页
     */
    public function pageQueryBySupplier(){
        $m = new M();
        return WSTGrid($m->pageQueryBySupplier());
    }
    /**
     * 获取指定记录
     */
    public function tologmoneys(){
        $m = new M();
        $object = $m->getUserInfoByType();
        $this->assign("p",(int)input("p"));
        $this->assign("src",input("src"));
        $this->assign("object",$object);
        return $this->fetch("list_log");
    }
    /**
     * 获取资金记录
     */
    public function pageQuery(){
        $m = new M();
        return WSTGrid($m->pageQuery());
    }

    /**
     * 获取充值记录
     */
    public function charge(){
        $m = new M();
        $this->assign("p",(int)input("p"));
        return $this->fetch("list_charge");
    }

    /**
     * 获取资金记录
     */
    public function pageQueryByCharge(){
        $m = new M();
        return WSTGrid($m->pageQuery(4));
    }

    /**
     * 跳去新增界面
     */
    public function toAdd(){
        $m = new M();
        $object = $m->getUserInfoByType();
        $this->assign("object",$object);
        return $this->fetch("box");
    }

    /**
     * 新增
     */
    public function add(){
        $m = new M();
        return $m->addByAdmin();
    }

    /**
     * 阶段汇总
     */
    public function phaseSummary(){
        $m = new M();
        $rs = $m->phaseSummary();
        return WSTReturn("",1,$rs);
    }

    /**
     * 财务统计
     */
    public function financeIndex(){
        $m = new M();
        $data = $m->phaseSummary();
        $this->assign("data",$data);
        $this->assign("p",(int)input("p"));

        return $this->fetch("finance/list");
    }

    /**
     * 获取资金记录
     */
    public function rechangePageQuery(){
        $m = new M();
        return WSTGrid($m->rechangePageQuery());
    }
}
