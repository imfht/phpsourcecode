<?php
namespace wstmart\shop\controller;
use wstmart\common\model\CashDraws as M;
use wstmart\common\model\Shops as MShops;
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
 * 提现记录控制器
 */
class Cashdraws extends Base{
    protected $beforeActionList = ['checkAuth'];
    /**
     * 查看商家资金流水
     */
    public function shopIndex(){
        return $this->fetch('cashdraws/list');
    }
    /**
     * 获取用户数据
     */
    public function pageQueryByShop(){
        $shopId = (int)session('WST_USER.shopId');
        $data = model('CashDraws')->pageQuery(1,$shopId);
        return WSTGrid($data);
    }
    /**
     * 申请提现
     */
    public function toEditByShop(){
        $this->assign('object',model('shops')->getShopAccount());
        $m = new MShops();
        $shopId = (int)session('WST_USER.shopId');
        $shop = $m->getFieldsById($shopId,["shopMoney","rechargeMoney"]);
        $this->assign('shop',$shop);
        return $this->fetch('cashdraws/box_draw');
    }
    /**
     * 提现
     */
    public function drawMoneyByShop(){
        $m = new M();
        return $m->drawMoneyByShop();
    }
}
