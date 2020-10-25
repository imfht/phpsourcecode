<?php
namespace wstmart\admin\controller;
use wstmart\admin\model\SysConfigs as M;
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
 * 商城配置控制器
 */
class Sysconfigs extends Base{
	
    public function index(){
    	$m = new M();
    	$object = $m->getSysConfigs();
    	$this->assign("object",$object);
    	return $this->fetch("edit");
    }
    
    /**
     * 保存
     */
    public function edit(){
    	$m = new M();
    	return $m->edit();
    }

    /**
     * 查看签到设置
     */
    public function sign(){
        $m = new M();
        $object = $m->getSysConfigsByType(3);
        $this->assign("object",$object);
        return $this->fetch("sign");
    }
    /**
     * 签到设置
     */
    public function editSign(){
        $m = new M();
        return $m->edit(3);
    }

    /**
     * 购物设置
     */
    public function buyConfig(){
        $m = new M();
        $object = $m->getSysConfigsByType(4);
        $this->assign("object",$object);
        return $this->fetch("buy");
    }
    /**
     * 购物设置
     */
    public function editBuyConfig(){
        $m = new M();
        return $m->edit(4);
    }

    /**
     * 通知设置
     */
    public function notifyConfig(){
        $m = new M();
        $object = $m->getSysConfigsByType(5);
        $this->assign("object",$object);
        $list = model('admin/staffs')->listQuery();
        $this->assign("list",$list);
        return $this->fetch("notify");
    }
    /**
     * 通知设置
     */
    public function editNotifyConfig(){
        $m = new M();
        return $m->edit(5);
    }
}
