<?php
namespace wstmart\admin\controller;
use wstmart\admin\model\ShopApplys as M;
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
class Shopapplys extends Base{
    
    /**
     * 跳去新增/编辑页面
     */
    public function toHandleApply(){
        $id = (int)input("id");
        $m = new M();
        if($id>0){
            $object = $m->getById($id);
        }else{
            $object = $m->getEModel('shop_applys');
        }
        $this->assign('object',$object);
        $this->assign("p",(int)input('p'));
        return $this->fetch("edit");
    }

    /**
     * 修改
     */
    public function handleApply(){
        $m = new M();
        return $m->edit();
    }
    /**
     * 删除
     */
    public function del(){
        $m = new M();
        return $m->del();
    }

    /**
     * 获取数据
     */
    public function getById(){
        $m = new M();
        return $m->getById((int)input("id"));
    }

    /**
     * 获取分页
     */
    public function pageQuery(){
        $m = new M();
        $this->assign("p",(int)input('p'));
        return WSTGrid($m->pageQuery());
    }
    
}
