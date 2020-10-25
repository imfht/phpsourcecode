<?php
namespace wstmart\shop\controller;
use wstmart\common\model\GoodsAppraises as M;
use wstmart\shop\model\GoodsAppraises as N;
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
 * 评价控制器
 */
class GoodsAppraises extends Base{
    protected $beforeActionList = ['checkAuth'];
    /**
     * 获取评价列表 商家
     */
    public function index(){
        return $this->fetch('goodsappraises/list');
    }
    // 获取评价列表 商家
    public function queryByPage(){
        $m = new N();
        return WSTGrid($m->queryByPage());
    }

    /**
     * 商家回复评价
     */
    public function shopReply(){
        $m = new M();
        return $m->shopReply();
    }
}
