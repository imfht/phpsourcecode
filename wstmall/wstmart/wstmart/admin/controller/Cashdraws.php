<?php
namespace wstmart\admin\controller;
use wstmart\admin\model\CashDraws as M;
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
 * 提现控制器
 */
class Cashdraws extends Base{

    public function index(){
        $this->assign("p",(int)input("p"));
    	return $this->fetch("list");
    }

    /**
     * 获取分页
     */
    public function pageQuery(){
        $m = new M();
        return WSTGrid($m->pageQuery());
    }

    /**
     * 跳去编辑页面
     */
    public function toHandle(){
        //获取该记录信息
        $m = new M();
        $this->assign('object', $m->getById());
        $this->assign("p",(int)input("p"));
        return $this->fetch("edit");
    }
    
    /**
    * 修改
    */
    public function handle(){
        $drawsStatus = (int)input('cashSatus',-1);
        $m = new M();
        if($drawsStatus==1){
            return $m->handle();
        }else{
            return $m->handleFail();
        }
    }

    /**
     * 查看提现内容
     */
    public function toView(){
        $m = new M();
        $this->assign('object', $m->getById());
        $this->assign("p",(int)input("p"));
        return $this->fetch("view");
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
     * 提现统计
     */
    public function statCashDrawal(){
        $m = new M();
        $rs = $m->statCashDrawal();
        return WSTGrid($rs);
    }
}
