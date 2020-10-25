<?php
namespace wstmart\mobile\controller;
use wstmart\common\model\ShopApplys as M;
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
class Shopapplys extends Base{
    // 前置方法执行列表
    protected $beforeActionList = [
        'checkAuth',
    ];
    /**
    * 跳去商家入驻页面
    */
    public function index(){
        if((int)WSTConf('CONF.isOpenShopApply')!=1)return;
        $m = new M();
        $um = new UM();
        $userId = (int)session('WST_USER.userId');
        // 获取是否已经填写商家入驻
        $isApply = $m->isApply();
        $rs = $um->getFieldsById($userId,'userPhone');
        $this->assign('isApply',$isApply);
        $this->assign('userPhone',$rs['userPhone']);
    	return $this->fetch('users/shopapplys/shop_applys');
    }

    /**
     * 保存商家入驻
     */
    public function add(){
        if((int)WSTConf('CONF.isOpenShopApply')!=1)return WSTReturn('未开启商家入驻');
        $m = new M();
        return $m->add();
    }
}
